<?php
namespace v;
/*
 * v framework
 * 
 * v框架控制器对象
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: Proces.php 6827 2015-08-07 14:25:14Z wangyong $
 */

//namespace v;

//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));

/**
 * 实体类
 * 模型与控制器类从此继承
 */
define('vProcesOnlyINPUT', 1);
define('vProcesAllINPUT', 0);
define('vProcesFieldKEY', 1);
define('vProcesFieldINFO', 0);

abstract class Proces extends Svc {

    /**
     * 只允许效验字段接收输入
     */
    const ONLY_INPUT = 1;

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

    /**
     * 判断是否多条数据
     * @param array $data
     * @return boolean
     */
    public static function isMulti(&$data) {
        $isMulti = isset($data[0]) && is_array($data[0]);
        return $isMulti;
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
     * 减去数据项
     * @param array $data
     * @return v\Proces
     */
    public function subs($data) {
        // 不带key减去数据中的某些键
        if (isset($data[0])) {
            $data = array_flip($data);
            if (!$this->multi) {
                $this->idata = array_diff_key($this->idata, $data);
            } else {
                foreach ($this->idata as $key => $item) {
                    $this->idata[$key] = array_diff_key($item, $data);
                }
            }
            return $this;
        }
        // 带key减去数据中相同的值
        if (!$this->multi) {
            $this->idata = array_same_filter($this->idata, $data);
        } else {
            foreach ($this->idata as $key => $item) {
                $this->idata[$key] = array_same_filter($item, $data);
            }
        }
        return $this;
    }

    /**
     * 添加数据项,不转换,不检查字段
     * 支持key,value
     * @param array|string $data
     * @param mixed $value
     * @return \v\Proces
     */
    public function adds($data, $value = null) {
        if (is_string($data)) {
            $data = [$data => $value];
        }
        if (empty($this->idata)) {
            $this->idata = $data;
            $this->multi = $this->isMulti($data);
        } elseif ($this->multi === $this->isMulti($data)) {
            $this->idata = array_merge($this->idata, $data);
        } else {
            throw new Exception('Dimension of the array not same');
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
                    $item[$field] = Cvt::shift($value, $rules, $this);
                }
            }
        }
        return $item;
    }

    /**
     * 转换一行数据，cast别名
     * @param array $data
     * @param boolean $only_input 是否只取可输入数据
     * @return array
     */
    public function conv(&$data, $only_input = false) {
        return $this->cast($data, $only_input);
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
        $message = [];
        foreach ($this->fields as $field => $rules) {
            $value = isset($data[$field]) ? $data[$field] : null;
            if ((!is_null($value) || $required) && !empty($rules[1])) {
                if ($rs = Chk::valid($value, $rules[1], $this))
                    $message[$field] = $rs;
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

}