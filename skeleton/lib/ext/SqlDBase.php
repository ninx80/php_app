<?php
namespace v\ext;
use v;
/*
 * v framework
 * 
 * v框架sql数据库驱动
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: SqlDBase.php 8329 2015-11-12 11:30:50Z liuyang $
 */

//namespace v\ext;

//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));

//use v;

/**
 * sql数据库驱动类
 */
class SqlDBase extends v\DBase {

    /**
     * 数据库配置
     * @var array
     */
    protected $configs = [
        'prefix' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => 5432,
        'dbname' => 'test',
        'username' => '',
        'password' => '',
        'options' => []
    ];

    /**
     * 数据库连接集合链接
     * @var array
     */
    private static $mconns = [];

    /**
     * 数据库链接
     * @var array
     */
    protected $hdb = null;

    /**
     * 数据表连接
     * @var MongoCollection
     */
    protected $hconn = null;

    /**
     * 最后插入的ID
     * @var int
     */
    protected $lastID = null;

    /**
     * 查询对象
     * @var PDOStatement
     */
    protected $stmt = null;

    /**
     * 是否显示sql
     * @var boolean
     */
    protected static $dsql = false;

    /**
     * 取得数据库连接
     * @return MongoDB
     */
    public function hdb() {
        if (empty($this->hdb)) {
            $conf = $this->configs;
            $port = array_keys_value('port', $conf, 5432);
            $dsn = "{$conf['prefix']}:host={$conf['host']};port={$port};dbname={$conf['dbname']};";
            $key = md5($dsn);
            if (empty(self::$mconns[$key])) {
                $options = array_keys_value('options', $conf, []);
                self::$mconns[$key] = new \PDO($dsn, $conf['username'], $conf['password'], $options);
                self::$mconns[$key]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
            }
            $this->hdb = self::$mconns[$key];
        }
        return $this->hdb;
    }

    /**
     * 取得数据连接器
     * @return MongoCollection
     */
    public function hconn() {
        if (empty($this->hconn)) {
            $this->hconn = $this->hdb();
        }
        return $this->hconn;
    }

    /**
     * 建立索引
     * @param array $indexes
     * @return
     */
    public function indexes($indexes) {
        foreach ($indexes as $items) {
            
        }
        return $this;
    }

    /**
     * 显示mysql
     */
    public static function dsql() {
        self::$dsql = true;
    }

    /**
     * 选择数据表
     * @param string $table
     */
    public function selectTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 执行sql
     * @param string $sql
     * @return init
     */
    public function exec($sql) {
        if (self::$dsql)
            echo "$sql\n";
        return $this->hdb()->exec($sql);
    }

    /**
     * 设置预查询
     * @param string $sql
     * @return PDOStatement 
     */
    public function prepare($sql) {
        if (self::$dsql)
            echo "$sql\n";
        return $this->hdb()->prepare($sql);
    }

    /**
     * 查询sql语句
     * @param string $sql
     * @return array
     */
    public function query($sql) {
        if (self::$dsql)
            echo "$sql\n";
        return $this->hdb()->query($sql);
    }

    /**
     * 把数据转为sql预查询中的SET部分
     * @param array $data
     * @param string $sql
     * @return string
     */
    protected function sqlPrepSet(&$data) {
        $str = '';
        foreach ($data as $field => $value) {
            $field = strtr($field, ':', '');
            $str .= "$field=:$field,";
        }
        return $str;
    }

    /**
     * 数据转为sql预查询中的数据格式
     * @param array $data
     * @return array
     */
    protected function sqlPrepParam(&$data) {
        $param = array();
        foreach ($data as $field => $value) {
            $param[":$field"] = $value;
        }
        return $param;
    }

    /**
     * 数据欲查询value生成
     * @param array $data
     * @return string
     */
    protected function sqlPrepValues(&$data) {
        $field = '(';
        $value = 'VALUES(';
        foreach ($data as $key => $val) {
            $field .= "$key, ";
            $value .= ":$key, ";
        }
        $field = trim($field, ', ') . ')';
        $value = trim($value, ', ') . ')';
        return "$field $value";
    }

    /**
     * 数据转义
     */
    protected function sqlAddslashes($value) {
        $value = is_string($value) ? '\'' . addslashes($value) . '\'' : $value;
        return $value;
    }

    /**
     * 数据插入 value生成
     * @param array $data
     * @return string
     */
    protected function sqlValues(&$data) {
        $field = '(';
        $value = 'VALUES(';
        foreach ($data as $key => $val) {
            $field .= "$key, ";
            $value .= $this->sqlAddslashes($val) . ', ';
        }
        $field = trim($field, ', ') . ')';
        $value = trim($value, ', ') . ')';
        return "$field $value";
    }

    /**
     * 生成set
     * @param string|array $data
     * @return string
     */
    public function sqlSet(&$data) {
        $sql = ' SET ';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if ($k == '$set') {
                    foreach ($v as $k1 => $v1) {
                        if (!is_null($v1))
                            $sql .= (" $k1 = " . $this->sqlAddslashes($v1) . ',');
                    }
                } elseif ($k == '$inc') {
                    foreach ($v as $k1 => $v1) {
                        if (!is_null($v1))
                            $sql .= " $k1 = $k1 + $v1,";
                    }
                } elseif (!is_null($v)) {
                    if (is_array($v))
                        $v = implode(',', $v);
                    $sql .= (" $k = " . $this->sqlAddslashes($v) . ',');
                }
            }
            $sql = trim($sql, ',');
        } else
            $sql .= $data;
        return $sql;
    }

    /**
     * 构造where
     * @param string|array $criteria
     * @return string
     */
    public function sqlWhere(&$criteria) {
        $sql = '';
        if (!empty($criteria)) {
            $sql = " WHERE " . (is_string($criteria) ? $criteria : $this->sqlWhere2($criteria));
        }
        return $sql;
    }

    /**
     * mongo查询条件to sql
     * @param array $criteria
     * @param blean $or 逻辑或关系
     * @return string
     */
    protected function sqlWhere2(&$criteria) {
        $sql = '';
        $ops = array('$lt' => '<', '$gt' => '>', '$lte' => '<=', '$gte' => '>=', '$like' => 'LIKE');
        foreach ($criteria as $k => $v) {
            if ($k == '$or') { // or关系
                $sql .= ' (';
                foreach ($v as $v1) {
                    $sql .= $this->sqlWhere2($v1, true) . ' OR ';
                }
                $sql = substr($sql, 0, -4) . ') ';
            } else { // and关系
                if (is_array($v)) { // 数组计算逻辑符
                    foreach ($v as $k1 => $v1) {
                        if ($k1 == '$ne') {
                            $sql .= " $k != " . $this->sqlAddslashes($v1);
                        } elseif ($k1 == '$in' || $k1 == '$nin') {
                            if (is_array($v1)) {
                                if (is_string(reset($v1)))
                                    $v1 = '\'' . implode('\',\'', $v1) . '\'';
                                else
                                    $v1 = implode(',', $v1);
                            }
                            $sql .= " $k " . ($k1 == '$nin' ? 'NOT ' : '') . "IN ($v1)";
                        } elseif (isset($ops[$k1])) {
                            $sql .= " $k {$ops[$k1]} " . $this->sqlAddslashes($v1);
                        } else {
                            $sql .= " $k $k1 " . $this->sqlAddslashes($v1);
                        }
                        $sql .= ' AND';
                    }
                    $sql = substr($sql, 0, -3);
                } elseif (is_null($v)) {
                    $sql .= " $k IS NULL";
                } else {
                    $sql .= " $k = " . $this->sqlAddslashes($v);
                }
            }
            $sql .= " AND";
        }
        return substr($sql, 0, -3);
    }

    /**
     * 构造select
     * @param array|string $fields
     * @return string
     */
    public function sqlSelect($fields) {
        $sql = $fields;
        if (empty($fields)) {
            $sql = '*';
        } elseif (is_array($fields)) {
            $sql = implode(',', array_keys($fields)) . ',';
        }
        $sql = 'SELECT ' . trim($sql, ',') . ' ';
        return $sql;
    }

    /**
     * 构造排序sql
     * @param array $fields
     * @return string
     */
    public function sqlSort($fields) {
        $sql = '';
        if (!empty($fields)) {
            foreach ($fields as $field => $order) {
                $sql .= "$field " . ($order === -1 ? 'DESC' : 'ASC') . ',';
            }
            $sql = ' ORDER BY ' . trim($sql, ',') . ' ';
        }
        return $sql;
    }

    /**
     * 构造limit
     * @param int $limit
     * @return string
     */
    public function sqlLimit($limit) {
        $sql = '';
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
        }
        return $sql;
    }

    /**
     * 构造offset
     * @param int $skip
     * @return string
     */
    public function sqlOffset($skip) {
        $sql = '';
        if ($skip > 0) {
            $sql .= " OFFSET $skip";
        }
        return $sql;
    }

    /**
     * 查询单条
     * @param array $fields 查询的字段 
     * @return array
     */
    public function findOne($query = [], $fields = []) {
        $sql = $this->sqlSelect($fields) . " FROM {$this->table}"
                . $this->sqlWhere($query)
                . $this->sqlLimit(1);

        $stmt = $this->query($sql);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 查询表
     * @param array $fields 查询的字段 
     * @return array
     */
    public function find($query = [], $fields = []) {
        $this->select($query, $fields);
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 查询数据
     * @param type $query
     * @param type $fields
     * @return \v\ext\SqlDBase
     */
    public function select($query = [], $fields = []) {
        $sql = $this->sqlSelect($fields) . " FROM {$this->table}"
                . $this->sqlWhere($query)
                . $this->sqlSort($this->sort)
                . $this->sqlLimit($this->limit)
                . $this->sqlOffset($this->skip);
        $this->stmt = $this->query($sql);
        return $this;
    }

    /**
     * 取得一条查询数据
     * @return array
     */
    public function next() {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 处理最近结果的错误消息
     * @param PDOStatement $stmt
     * @param boolean $rs
     */
    protected function resultLastError($rs) {
        $this->lastError = null;
        if ($rs === false) {
            $this->lastError = implode(', ', $this->hdb()->errorinfo());
            v\Err::add($this->lastError);
        }
        return $this;
    }

    /**
     * 开始事务
     * @return
     */
    public function beginTransaction() {
        $hdb = $this->hdb();
        $hdb->beginTransaction();
        // 设置到异常抛出模式
        $hdb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $this;
    }

    /**
     * 提交事务
     * @return
     */
    public function commitTransaction() {
        $hdb = $this->hdb();
        $hdb->commit();
        // 设置到异常抛出模式
        $hdb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        return $this;
    }

    /**
     * 回滚事务
     * @return \v\ext\SqlDBase
     */
    public function backTransaction() {
        $hdb = $this->hdb();
        $hdb->rollBack();
        // 设置到异常抛出模式
        $hdb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        return $this;
    }

    /**
     * 插入数据，多条或单条
     * @param array $data 要插入的数据，为空则使用sets数据，需要先检查数据有效性
     * @return boolean
     */
    public function insert(&$data) {
        $multi = isset($data[0]) && is_array($data[0]);
        $this->lastID = 0;
        $rs = 0;
        if ($multi) {
            // 多条插入
            $item = reset($data);
            $sql = "INSERT INTO {$this->table} " . $this->sqlPrepValues($item);
            $stmt = $this->prepare($sql);
            foreach ($data as $item) {
                if ($stmt->execute($this->sqlPrepParam($item))) {
                    $rs++;
                    $this->lastID = empty($item['_id']) ? $this->hdb()->lastInsertId('_id') : $item['_id'];
                }
            }
            if ($rs === 0)
                $rs = false;
        } else {
            // 单条插入
            $sql = "INSERT INTO $this->table " . $this->sqlValues($data);
            $rs = $this->exec($sql);
            if ($rs) {
                $this->lastID = empty($data['_id']) ? $this->hdb()->lastInsertId('_id') : $data['_id'];
            }
        }
        $this->resultLastError($rs);
        return $rs !== false;
    }

    /**
     * 更新数据，多条或单条
     * @param array $data
     * @return boolean
     */
    public function update($criteria, &$data) {
        $sql = "UPDATE {$this->table}" . $this->sqlSet($data)
                . $this->sqlWhere($criteria);

        $rs = $this->exec($sql);
        $this->resultLastError($rs);
        return $rs !== false;
    }

    /**
     * 删除数据
     * @return boolean
     */
    public function remove($criteria) {
        $sql = "DELETE FROM $this->table" . $this->sqlWhere($criteria);

        $rs = $this->exec($sql);
        $this->resultLastError($rs);
        return $rs !== false;
    }

    /**
     * 删除数据表
     * @return boolean
     */
    public function drop() {
        $sql = "DROP TABLE $this->table";

        $rs = $this->exec($sql);
        $this->resultLastError($rs);
        return $rs !== false;
    }

    /**
     * 取得数据条数
     * @return int
     */
    public function count($query, $limit = null) {
        $sql = $this->sqlSelect('COUNT(_id)') . " FROM {$this->table}"
                . $this->sqlWhere($query);
        if ($limit)
            $sql .= $this->sqlLimit($limit);
        $stmt = $this->query($sql);
        return $stmt->fetchColumn();
    }

    /**
     * 取得最后生成的ID
     * @return string
     */
    public function lastID() {
        return $this->lastID;
    }

}