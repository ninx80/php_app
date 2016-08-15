<?php
namespace v;
use v\ext;
/*
 * v framework
 * 
 * v框架控制器对象
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: Model.php 8581 2015-11-20 02:19:18Z wangyong $
 */


/**
 * 模型类
 * 用户的模型类从此继承
 */
abstract class Model {

    /**
     * 插入数据ready hook
     */
    const HOOK_INSERT_DATA_READY = 1;

    /**
     * 更新数据ready hook
     */
    const HOOK_UPDATE_DATA_READY = 2;

    /**
     * 插入数据成功 hook
     */
    const HOOK_INSERT_DATA_SUCCESS = 3;

    /**
     * 更新数据成功 hook
     */
    const HOOK_UPDATE_DATA_SUCCESS = 4;

    /**
     * 删除数据成功 hook
     */
    const HOOK_REMOVE_DATA_SUCCESS = 5;

    /**
     * 多条定义
     */
    const MULTI = 1;

    /**
     * 单条定义
     */
    const ONE = 0;

    /**
     * 只允许效验字段接收输入
     */
    const ONLY_INPUT = 1;

    /**
     * 所有字段接收输入
     */
    const CAN_INPUT = 0;

    /**
     * 所有操作
     */
    const ALL = 0;

    /**
     * 查找操作
     */
    const FIND = 1;

    /**
     * 新增操作
     */
    const INSERT = 2;

    /**
     * 更新操作
     */
    const UPDATE = 3;

    /**
     * 删除操作
     */
    const REMOVE = 4;

    /**
     * 增改查操作
     */
    const ALTER = 5;

    /**
     * 删除表操作
     */
    const DROP = 6;

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
     * 条件定义
     * @var array
     */
    protected $where = null;

    /**
     * 查询字段定义
     * @var array
     */
    public $field = null;

    /**
     * find的数据
     * @var array
     */
    protected $fdata = [];

    /**
     * 原数据
     * 保存下一次查询要用的数据
     * @var array
     */
    protected $odata = [];

    /**
     * 是否锁定
     * 不rest field\sort\limit,直到一次find操作解锁
     * @var boolean
     */
    protected $locked = false;

    /**
     * 是否自动生成uuid
     * @var string
     */
    protected $guuid = true;

    /**
     * 数据表,子类必须定义
     * @var string
     */
    protected $table = null;

    /**
     * 数据库处理对象
     * @var v\DBase
     */
    protected $dbase = null;

    /**
     * 数据库配置，同数据库驱动的配置
     * @var array
     */
    protected $dbconfs = [];

    /**
     * 数据库类型
     * 子类配置，如果模型未配置 dbtype-driver，则用该类型作为数据库处理类
     * @var string
     */
    protected $dbtype = 'mongodb';

    /**
     * 数据cursor，next使用
     * @var v\DBase
     */
    protected $cursor = null;

    /**
     * 附加设置
     * @var array
     */
    protected $options = [];

    /**
     * last ID
     * @var string
     */
    protected $lastID = null;

    /**
     * 最后的操作
     * @var int
     */
    protected $lastOP = null;

    /**
     * 索引定义，由子类定义
     * 每个数组代表一个索引
     * @var array
     */
    protected $indexes = [
            // ['filedname'=>-1]
    ];

    /**
     * 允许的排序字段，子类定义
     */
    protected $sorts = [
            // '-field1+field2'
    ];
    


    /**
     * 所有字段接收输入
     */
    const ALL_INPUT = 0;

    /**
     * 字段名称
     */
    const FIELD_KEY = 1;

    /**
     * 字段所有信息
     */
    const FIELD_INFO = 0;

    /**
     * 是否允许操作
     * @var boolean
     */
    protected $canop = true;

    /**
     * 是否多条数据
     * @var boolean
     */
    protected $multi = null;

    /**
     * 要操作的数据
     * @var array
     */
    protected $idata = [];

    /**
     * 数据字段，由子类定义
     * 每个字段必须定义，如果不接受用户输入，则不定义效验规则
     * 转换规则必须定义
     * @var array
     */
    protected $fields = [
            //'fieldname' => [['convert function name1', 'convert function name2'], ['check function name, otherparam1, otherparam2', ['check functon name', 'functon params']]]
    ];
    
    public function __construct() {
                // 检查table属性是否配置
        if (empty($this->table)) {
            throw new PropertyException('Must be set table property');
        }
        // 检查dbtype熟悉是否配置
        if (empty($this->dbtype)) {
            throw new PropertyException('Must be set dbtype property');
        }
        $this->reset();
    }
    /**
     * 配置
     * @var array
     */
    protected $configs = [
        'max-limit' => 100, // 最多查询条数
        'mongodb-driver' => 'v\ext\MongoDBase',
        'pgsql-driver' => 'v\ext\SqlDBase',
        'mysql-driver' => 'v\ext\SqlDBase'
    ];
    public static function isMulti(&$data) {
        $isMulti = isset($data[0]) && is_array($data[0]);
        return $isMulti;
    }
    /**
     * 重设
     * @return \v\Model
     */
    public function reset($state = self::ALL) {
        if (in_array($state, [self::ALL, self::FIND, self::UPDATE, self::REMOVE, self::ALTER])) {
            $this->where = [];
        }
        if (in_array($state, [self::ALL, self::UPDATE, self::REMOVE, self::ALTER])) {
            $this->canop = true;
            $this->idata = [];
            $this->multi = false;
        }
        if (in_array($state, [self::ALL, self::FIND])) {
            $this->limit = array_keys_value('max-limit', $this->configs, 100);
            if (!$this->locked) {
                $this->skip = 0;
                $this->sort = [];
                $this->field = [];
                $this->cursor = null;
            }
            $this->fdata = [];
        }
        $this->options = [];
        return $this;
    }

    /**
     * 锁定skip，sort，field, cursor数据，reset时不清空
     */
    public function locked($state = true) {
        $this->locked = $state;
        return $this;
    }

    /**
     * 取得数据|设置源数据
     * @param string $key
     * @return mixed
     */
    public function olds($key = null) {
        if (is_array($key)) {
            $this->odata = $key;
            return $this;
        }
        $data = empty($this->odata) ? $this->fdata : $this->odata;
        return is_null($key) ? $data : array_keys_value($key, $data);
    }

    /**
     * 给数据生成唯一ID
     * @param array $data
     */
    public function guuid() {
        if ($this->isMulti($this->idata)) {
            foreach ($this->idata as $k => $item) {
                if (!isset($item['_id']))
                    $this->idata[$k]['_id'] = uniqid12();
            }
            $this->lastID = end($this->idata)['_id'];
        } else {
            if (!isset($this->idata['_id']))
                $this->idata['_id'] = uniqid12();
            $this->lastID = $this->idata['_id'];
        }
        return $this;
    }

    /**
     * 取多少条
     * @param int $num
     * @return \v\Model
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
     * @return \v\Model
     */
    public function skip($num = null) {
        if (is_null($num))
            return $this->skip;
        $this->skip = $num;
        return $this;
    }

    /**
     * 排序
     * @param array|string $sorts，如果为字符串需要检测是否允许排序
     *      string like -filed1+field2
     * @return \v\Model
     */
    public function sort($sorts = null) {
        if (is_null($sorts))
            return $this->sort;
        if (!empty($sorts)) {
            if (is_string($sorts)) {
                // 检查是否允许排序
                if (!empty($this->sorts) && !in_array($sorts, $this->sorts))
                    throw new Exception("must not use [$sorts] sort");
                $fields = explode(',', trim(strtr($sorts, array('-' => ',-', '+' => ',+', ' ' => ',+')), ','));
                $sorts = [];
                foreach ($fields as $field) {
                    $state = substr($field, 0, 1) == '-' ? -1 : 1;
                    $field = trim($field, '+ -');
                    $sorts[$field] = $state;
                }
            }
            $this->sort = $sorts;
        }
        return $this;
    }

    /**
     * 设置find取得的字段
     * @param array|string $fields
     *      string format +field1+field2 or -field1-field2
     * @return \v\Model
     */
    public function field($fields = null) {
        if (is_null($fields))
            return $this->field;
        if (!empty($fields)) {
            if (is_array($fields) && isset($fields[0])) {
                $this->field = array_fill_keys($fields, 1);
            } else {
                if (is_string($fields)) {
                    $state = substr($fields, 0, 1) == '-' ? 0 : 1;
                    $fields = explode(',', trim(strtr($fields, array('-' => ',', '+' => ',', ' ' => ',')), ','));
                    $fields = array_fill_keys($fields, 1);
                } else {
                    $state = reset($fields);
                }

                if ($state == 0) {
                    // 排除某字段， 在现有字段上排除
                    if (empty($this->field)) {
                        $this->field = array_keys($this->fields);
                        $this->field = array_fill_keys($this->field, 1);
                    }
                    foreach ($fields as $field => $value) {
                        unset($this->field[$field]);
                    }
                } else {
                    $this->field = $fields;
                }
            }
        }
        return $this;
    }

    /**
     * where条件
     * @param array $data
     * @return \v\Model
     */
    public function where($data = null) {
        if (is_null($data))
            return $this->where;
        $this->reset(self::FIND)->where = $data;
        return $this;
    }

    /**
     * 设置增删查改的附加条件
     * @param array $options
     * @return \v\Model
     */
    public function options($options = null) {
        if (is_null($options))
            return $this->options;
        $this->options = $options;
        return $this;
    }

    /**
     * 取得数据库对象
     * 可在模型中配置dbclass-class，指向实际的数据模型
     */
    public function dbase() {
        // 获取数据库实例
        if (empty($this->dbase)) {
            $dbclass = $this->dbtype;
            if (!empty($this->configs["{$dbclass}-driver"]))
                $dbclass = $this->configs["{$dbclass}-driver"];
            $this->dbase = new $dbclass($this->dbconfs);
            //$this->dbase->selectTable($this->table);
        }
        return $this->dbase->selectTable($this->table);
    }

    /**
     * 切换数据库配置
     * @param array $conf
     * @return \v\Model
     */
    public function dbConf($conf = null) {
        // 设置数据库配置
        if (!is_null($conf)) {
            if ($conf != $this->dbconfs) {
                $this->dbconfs = $conf;
                $this->dbase = null;
            }
            return $this;
        }
        return $this->dbconfs;
    }

    /**
     * 取得最后生成的ID
     * @return string
     */
    public function lastID() {
        if ($this->lastID)
            return $this->lastID;
        return $this->dbase()->lastID();
    }

    /**
     * 查询数据
     * @param array $fields
     * @return array
     */
    public function findOne($fields = null) {
        if (!is_null($fields))
            $this->field($fields);

        $conn = $this->dbase()->options($this->options);
        $this->fdata = $conn->findOne($this->where, $this->field);
        $this->locked = false;
        $this->lastOP = self::FIND;
        return $this->fdata;
    }

    /**
     * 取得数据，单行与多行
     * @param array|string 查询字段
     * @return array
     */
    public function find($fields = null, $multi = true) {
        if (!is_null($fields))
            $this->field($fields);

        $conn = $this->dbase()->options($this->options);

        $conn->skip($this->skip);
        $conn->limit($multi ? $this->limit : 1);
        $conn->sort($this->sort);

        $this->fdata = $conn->find($this->where, $this->field);
        // 排序的单条查询
        if (!$multi && !empty($this->fdata)) {
            $this->fdata = reset($this->fdata);
        }
        $this->locked = false;
        $this->lastOP = self::FIND;
        return $this->fdata;
    }

    /**
     * 取得下一条数据
     * @return array
     */
    public function next() {
        if (empty($this->cursor)) {
            $conn = $this->dbase()->options($this->options);

            $conn->skip($this->skip);
            $conn->limit($this->limit);
            $conn->sort($this->sort);

            $this->cursor = $conn->select($this->where, $this->field);
        }
        $data = $this->cursor->next();
        if (empty($data)) {
            // 数据为空时清楚锁定
            $this->locked = false;
            $this->lastOP = self::FIND;
        }
        return $data;
    }

    /**
     * 插入数据
     * @param array $data 要插入的数据，如果无数据，则使用sets的数据
     * @return boolean 是否成功
     */
    public function insert($data = null) {
        if (!$this->canop)
            return false;
        if (!empty($data))
            $this->idata = $data;
        
        if (empty($this->idata)) {
            halt('Require insert data');
            return false;
        }

        if ($this->triHook(self::HOOK_INSERT_DATA_READY, $this->idata))
            return false;
        
        if (!$this->can())
            return false;

        if ($this->guuid)
            $this->guuid();
        $conn = $this->dbase()->options($this->options);
        $rs = $conn->insert($this->idata);
        if ($rs) {
            $this->triHook(self::HOOK_INSERT_DATA_SUCCESS, $this->idata);
        }
        $this->canop = true;
        $this->lastOP = self::INSERT;
        return $rs;
    }

    /**
     * 更新数据
     * @param array $data 要插入的数据，如果无数据，则使用sets的数据
     *      更新时多条数据则不更新
     * @return boolean 是否成功
     */
    public function update($data = null) {
        if (!$this->canop)
            return false;
        
        if (!empty($data))
            $this->idata = $data;

        if ($this->triHook(self::HOOK_UPDATE_DATA_READY, $this->idata, $this->where))
            return false;

        if (!$this->can())
            return false;
        
        $conn = $this->dbase()->options($this->options);
        $rs = $conn->update($this->where, $this->idata);
        if ($rs) {
            $this->triHook(self::HOOK_UPDATE_DATA_SUCCESS, $this->idata, $this->where);
        }

        $this->canop = true;
        $this->lastOP = self::UPDATE;
        return $rs;
    }

    /**
     * 更新一条数据
     * @param array $data
     * @return boolean 是否成功
     */
    public function updateOne($data = null) {
        $this->options['multiple'] = false;
        return $this->update($data);
    }

    /**
     * 删除数据
     * @return boolean 是否成功
     */
    public function remove() {
        if (!empty($this->where)) {
            $conn = $this->dbase()->options($this->options);
            $rs = $conn->remove($this->where);
            if ($rs) {
                $this->triHook(self::HOOK_REMOVE_DATA_SUCCESS, $this->where);
            }
            return $rs;
        }
        $this->canop = true;
        $this->lastOP = self::REMOVE;
        halt('Can only remove with criteria');
        return false;
    }

    /**
     * 删除数据表
     * @return boolean 是否成功
     */
    public function drop() {
        $conn = $this->dbase()->options($this->options);
        $rs = $conn->drop();
        if (!empty($rs) && !empty($rs['ok'])) {
            return TRUE;
        }
        $this->canop = true;
        $this->lastOP = self::DROP;
        halt('Drop error');
        return false;
    }

    /**
     * 取得数据条数
     * @return int
     */
    public function count($limit = null) {
        $conn = $this->dbase()->options($this->options);
        return $conn->count($this->where, $limit);
    }

    /**
     * 创建索引
     */
    public function indexes() {
        if (!empty($this->indexes))
            $this->dbase()->indexes($this->indexes);
        return $this;
    }


    /**
     * 取得数据
     * @param string $key
     * @return mixed
     */
    public function gets($key = null) {
        if (is_null($key))
            return $this->idata;
        return array_keys_value($key, $this->idata);
    }

    /**
     * 设置字段数据
     * insert与update使用
     * 如果不接受输入，则没定义效验的字段不接收
     * @param array $params
     * @param boolean $only_input 是否接受输入
     * @return v\Proces
     */
    public function sets(&$params, $only_input = false) {
        $this->multi = $this->isMulti($params);
        if (!$this->multi) {
            $this->idata = $this->cast($params, $only_input);
        } else {
            // 多行数据转换
            $this->idata = [];
            foreach ($params as $data) {
                $this->idata[] = $this->cast($data, $only_input);
            }
        }
        return $this;
    }
    /**
     * 取得校验等处理结果
     * @return boolean
     */
    public function can() {
        $can = !empty($this->idata) && $this->canop;
        return $can;
    }

    /**
     * 转换一行数据
     * @param array $data
     * @param boolean $only_input 是否只取可输入数据
     * @return array
     */
    public function cast(&$data, $only_input = false) {
        $item = [];
        foreach ($data as $field => $value) {
            if (isset($this->fields[$field])) {
                if (!$only_input || isset($this->fields[$field][1])) {
                    $rules = $this->fields[$field][0];
                    foreach($rules as $fun){
                        $item[$field]=Cvt::$fun($value);
                    }
                }
            }
        }
        return $item;
    }

    /**
     * 效验sets的数据
     * @param boolean $required 是否必填
     * @return v\Proces
     */
    public function check($required = true) {
        if (!empty($this->idata)) {
            if (!$this->multi) {
                $message = $this->valid($this->idata, $required);
                if (!empty($message)) {
                    Err::add($message);
                    $this->canop = false;
                }
            } else {
                // 多行数据效验，忽略效验错误的数据
                foreach ($this->idata as $k => $data) {
                    $message = $this->valid($data, $required);
                    if (!empty($message)) {
                        unset($this->idata[$k]);
                        Err::add($message);
                        $this->canop = false;
                    }
                }
                if (!empty($this->idata))
                    $this->canop = true;
            }
        }
        // 效验不成功删除数据
        if (!$this->canop)
            $this->idata = [];
        return $this;
    }

    /**
     * 效验一行数据
     * @param array $data
     * @param boolean $required 是否必填
     * @return array
     */
    public function valid(&$data, $required = true) {
        $message = '';
        foreach ($this->fields as $field => $rules) {
            $value = isset($data[$field]) ? $data[$field] : null;
            if ((!is_null($value) || $required) && !empty($rules[1])) {
                if ($rs = Chk::valid($value, $rules[1])){
                    if (is_string($rs))
                        $message[$field] = $rs;
                    
                }
            }
        }
        return $message;
    }
    /**
     * 取得所有字段
     * @param boolean $can_input 是否只取可输入字段
     * @param boolean $all_info 是否取得所有信息
     * @return array
     */
    public function fields($only_input = 0, $field_key = 1) {
        $fields = $this->fields;
        if ($only_input) {
            foreach ($fields as $key => $field) {
                if (!isset($field[1])) {
                    unset($fields[$key]);
                }
            }
        }
        return $field_key ? array_keys($fields) : $fields;
    }
    /**
     * 添加钩子
     * @param string $type 事件类型
     * @param mix $fun 处理函数
     * @param mix $once 是否只执行一次
     */
    public function addHook($type, $fun, $once = false) {
        if (is_string($fun) && is_callable([$this, $fun]) || is_callable($fun)) {
            isset($this->events[$type]) or $this->events[$type] = [];
            if (!in_array([$fun, $once], $this->events[$type])) {
                $this->events[$type][] = [$fun, $once];
            }
        } else {
            throw new MethodException("Hook is not callable");
        }
        return $this;
    }

    /**
     * 触发钩子
     * @param string $type 事件类型
     */
    public function triHook($type, &$param1 = null, &$param2 = null) {
        $result = null;
        if (!empty($this->events[$type])) {
            foreach ($this->events[$type] as $k => $fun) {
                if (is_string($fun[0]))
                    $fun[0] = [$this, $fun[0]];
                if ($fun[1])
                    unset($this->events[$type][$k]);
                if (!is_null($param2)) {
                    if ($result = $fun[0]($param1, $param2))
                        break;
                } elseif (!is_null($param1)) {
                    if ($result = $fun[0]($param1))
                        break;
                } else {
                    if ($result = $fun[0]())
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * 取得定义的属性
     * @param string $name
     * @return mixed
     */
    public function attr($name) {
        return $this->$name;
    }

}