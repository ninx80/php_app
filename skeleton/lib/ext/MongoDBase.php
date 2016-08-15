<?php
namespace v\ext;
use v;
/*
 * v framework
 * 
 * v框架mongodb数据库驱动
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: MongoDBase.php 8581 2015-11-20 02:19:18Z wangyong $
 */

//namespace v\ext;

//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));

//use v;

/**
 * mongodb数据库驱动类
 */
class MongoDBase extends v\DBase {

    /**
     * 数据库配置
     * @var array
     */
    protected $configs = [
        'host' => '127.0.0.1',
        'dbname' => 'test',
        'username' => '',
        'password' => '',
        'options' => []
    ];

    /**
     * mongo链接集合
     * @var array
     */
    private static $mconns = [];

    /**
     * 数据库链接
     * @var array
     */
    private $hdb = null;

    /**
     * 数据连接
     * @var MongoCollection
     */
    private $hconn = null;

    /**
     * 最后插入的ID
     * @var MongoID
     */
    protected $lastID = null;

    /**
     * 取得数据库连接
     * @return MongoDB
     */
    public function hdb() {
        if (empty($this->hdb)) {
            $conf = $this->configs;
            array_merge_extend($this->configs, CONF('DB_CONFIG.MongoDBase'));
            //$conf = $this->configs;
            $dsn = 'mongodb://' . (empty($this->configs['username']) ? '' : "{$this->configs['username']}:{$this->configs['password']}@") . $this->configs['host'];
            $key = md5($dsn);
            if (empty(self::$mconns[$key])) {
                $options = array_keys_value('options', $this->configs, []);
                $dbClass = class_exists('MongoClient') ? 'MongoClient' : 'Mongo';

                self::$mconns[$key] = new $dbClass($dsn, $options);
            }
            $this->hdb = self::$mconns[$key]->selectDB($this->configs['dbname']);
        }
        return $this->hdb;
    }

    /**
     * 取得数据连接器
     * @return MongoCollection
     */
    public function hconn() {
        if (empty($this->hconn)) {
            $this->hconn = $this->hdb()->selectCollection($this->table);
        }
        return $this->hconn;
    }

    /**
     * 建立索引
     * @param array $indexes
     * @return
     */
    public function indexes($indexes) {
        $conn = $this->hconn();
        foreach ($indexes as $items) {
            $keys = reset($items);
            if (is_array($keys)) {
                $options = isset($items[1]) ? $items[1] : [];
            } else {
                $keys = $items;
                $options = [];
            }
            if (!isset($options['background']))
                $options['background'] = true;
            $conn->ensureIndex($keys, $options);
        }
        return $this;
    }

    /**
     * 选择集合
     * @param string $table
     * @return
     */
    public function selectTable($table) {
        $this->table = $table;
        $this->hconn = null;
        return $this;
    }

    /**
     * 解析查询条件
     * $like, $or 解析
     * @param array $query
     * @return array 
     */
    public function parseQuery(&$query) {
        foreach ($query as $key => $data) {
            if ($key == '$or') {
                foreach ($data as $k => $item) {
                    $query[$key][$k] = $this->parseQuery($item);
                }
            } elseif (is_array($data) && !empty($data['$like'])) {
                $value = preg_quote($data['$like']);
                $query[$key] = new \MongoRegex('/^' . strtr($value, array('%' => '.*')) . '$/i');
            }
        }
        return $query;
    }

    /**
     * 解析set
     * @param array $data
     * @return array
     */
    public function parseSets(&$data) {
        $item = $data;
        foreach ($item as $k => $v) {
            if (substr($k, 0, 1) != '$') {
                if (!is_null($v))
                    $item['$set'][$k] = $v;
                unset($item[$k]);
            }
        }
        return $item;
    }

    /**
     * 查询单条
     * @param array $fields 查询的字段 
     * @return array
     */
    public function findOne($query = [], $fields = []) {
        $conn = $this->hconn();
        $query = empty($query) ? [] : $this->parseQuery($query);
        return $conn->findOne($query, $fields);
    }

    /**
     * 查询表
     * @param array $fields 查询的字段 
     * @return array
     */
    public function find($query = [], $fields = []) {
        $this->select($query, $fields);
        return iterator_to_array($this->cursor, false);
    }

    /**
     * 查询数据
     * @param type $query
     * @param type $fields
     * @return \v\ext\SqlDBase
     */
    public function select($query = [], $fields = []) {
        $conn = $this->hconn();
        $query = empty($query) ? [] : $this->parseQuery($query);
        $cursor = $conn->find($query, $fields);
        if ($this->skip > 0)
            $cursor->skip($this->skip);
        if ($this->limit > 0)
            $cursor->limit($this->limit);
        if (!empty($this->sort))
            $cursor->sort($this->sort);
        $this->cursor = $cursor;
        return $this;
    }

    /**
     * 取得一条查询数据
     * @return array
     */
    public function next() {
        return $this->cursor->getNext();
    }

    /**
     * 处理最近结果的错误消息
     */
    protected function resultLastError($rs) {
        $this->lastError = null;
        if (!$rs && is_array($rs)) {
            $this->lastError = $this->hdb()->lastError()['err'];
            halt($this->lastError);
        }
        return $this;
    }

    /**
     * 插入数据，多条或单条
     * @param array $data 要插入的数据，为空则使用sets数据，需要先检查数据有效性
     * @return boolean
     */
    public function insert(&$data) {
        $multi = isset($data[0]) && is_array($data[0]);
        $rs = false;
        $this->lastID = 0;
        $conn = $this->hconn();
        try {
            if ($multi) {
                $options = $this->options;
                $options['continueOnError'] = true;
                $options['w'] = 1;
                $rs = $conn->batchInsert($data, $options);
                if ($rs)
                    $this->lastID = end($data)['_id'];
            } else {
                $rs = $conn->insert($data, $this->options);
                if ($rs)
                    $this->lastID = $data['_id'];
            }
        } catch (MongoException $e) {
            $rs = false;
        }
        $this->resultLastError($rs);
        return $rs;
    }

    /**
     * 更新数据，多条或单条
     * @param array $data
     * @return boolean
     */
    public function update($criteria, &$data) {
        $data1 = $this->parseSets($data);
        $criteria = $this->parseQuery($criteria);
        $options = $this->options;
        if (!isset($options['multiple']))
            $options['multiple'] = true;
        try {
            $rs = $this->hconn()->update($criteria, $data1, $options);
        } catch (MongoException $e) {
            $rs = false;
        }
        $this->resultLastError($rs);
        if (is_array($rs)) {
            $rs = $rs['n'];
            if ($rs === 0) {
                halt('Anything not updated');
            }
        }
        return $rs;
    }

    /**
     * 删除数据
     * @return boolean
     */
    public function remove($criteria) {
        $criteria = $this->parseQuery($criteria);
        $options = $this->options;
        if (!isset($options['justOne']))
            $options['justOne'] = false;
        try {
            $rs = $this->hconn()->remove($criteria, $options);
        } catch (MongoException $e) {
            $rs = false;
        }
        $this->resultLastError($rs);
        return $rs;
    }

    /**
     * 删除数据表
     * @return boolean
     */
    public function drop() {
        try {
            $rs = $this->hconn()->drop();
        } catch (\MongoException $e) {
            $rs = false;
        }
        $this->resultLastError($rs);
        return $rs;
    }

    /**
     * 取得数据条数
     * @return int
     */
    public function count($query, $limit = null) {
        $query = $this->parseQuery($query);
        $cursor = $this->hconn()->find($query, ['_id']);
        if ($limit) {
            $cursor->limit($limit);
            return $cursor->count(true);
        }
        return $cursor->count();
    }

    /**
     * 最后的ID
     * @return MongoID
     */
    public function lastID() {
        return $this->lastID;
    }

}