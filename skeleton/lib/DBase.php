<?php
namespace v;
/*
 * v framework
 * 
 * v框架控制器对象
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: DBase.php 8313 2015-11-12 09:52:01Z liuyang $
 */

//namespace v;

//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));

/**
 * 数据类
 * 数据库处理类从此继承
 */
abstract class DBase extends Svc {

    /**
     * 取数限制条数
     * @var init
     */
    protected $limit = null;

    /**
     * 跳过多少条
     * @var init
     */
    protected $skip = null;

    /**
     * 排序定义
     * @var array
     */
    protected $sort = null;

    /**
     * 数据表名
     * @var string
     */
    protected $table = null;

    /**
     * 数据库错误
     * @var string
     */
    protected $lastError = null;

    /**
     * 配置
     * @var array
     */
    protected $options = [];

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
     * @param array $config 数据库配置，如果不传改参数请配置数据处理模型
     * @throws Exception
     */
    public function __construct() {
        parent::__construct();
        if (func_num_args() > 0) {
            $args = func_get_args();
            $this->configs = array_merge($this->configs, $args[0]);
        }
    }

    /**
     * 取多少条
     * @param int $num
     * @return \v\DBase
     */
    public function limit($num = null) {
        if (is_null($num))
            return $this->limit;
        $this->limit = $num;
        return $this;
    }

    /**
     * 跳过多少条
     * @param int $num
     * @return \v\DBase
     */
    public function skip($num = null) {
        if (is_null($num))
            return $this->skip;
        $this->skip = $num;
        return $this;
    }

    /**
     * 排序
     * @param array $sorts
     * @return \v\DBase
     */
    public function sort($sorts = null) {
        if (is_null($sorts))
            return $this->sort;
        $this->sort = $sorts;
        return $this;
    }

    /**
     * 设置增删查改的附加条件
     * @param array $options
     * @return \v\DBase
     */
    public function options($options = null) {
        if (is_null($options))
            return $this->options;
        $this->options = $options;
        return $this;
    }

    /**
     * 获得执行错误
     * @return array
     */
    public function lastError() {
        return $this->lastError;
    }

    /**
     * 选择数据表
     * @param string 表名
     * @return v\DBase
     */
    abstract public function selectTable($table);

    /**
     * 取得数据，单行
     * @return array
     */
    abstract public function findOne($query = [], $fields = []);

    /**
     * 取得数据
     * @return array
     */
    abstract public function find($query = [], $fields = []);

    /**
     * 查询数据，不返回查询结果，和next配合使用
     * @return v\DBase
     */
    abstract public function select($query = [], $fields = []);

    /**
     * 取得下一条数据
     * @return array
     */
    abstract public function next();

    /**
     * 插入数据
     * @param array $data 要插入的数据
     * @return boolean 是否成功
     */
    abstract public function insert(&$data);

    /**
     * 更新数据
     * @param array $data 要插入的数据，如果无数据，则使用sets的数据
     * @return boolean 是否成功
     */
    abstract public function update($criteria, &$data);

    /**
     * 删除数据，多条
     * @return boolean 是否成功
     */
    abstract public function remove($criteria);

    /**
     * 删除数据表
     * @return boolean 是否成功
     */
    abstract public function drop();

    /**
     * 取得最后插入的ID
     * @return mixed
     */
    abstract public function lastID();

    /**
     * 取得数据条数
     * @param int $limit 最多限制条数，null不限制
     * @return int
     */
    abstract public function count($query, $limit = null);

    /**
     * 创建索引
     * @param array $indexes
     */
    abstract public function indexes($indexes);

    /**
     * 取得连接器
     * @return object
     */
    abstract public function hconn();
}

/**
 * 数据库异常
 */
class DBaseExcepton extends \Exception {
    
}

?>