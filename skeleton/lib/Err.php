<?php
namespace v;
class Err extends Fac {

    /**
     * 服务提供对象名
     * @var string
     */
    protected static $objname;

    /**
     * 服务提供对象
     * @var object
     */
    protected static $object;

}

/**
 * error服务类
 * 用户的error类从此继承
 */
class ErrSvc extends Svc{

    /**
     * 错误消息
     * @var array
     */
    protected $message = [];

    /**
     * 获得关键字消息
     * @return mixed 
     */
    public function get($key = null) {
        if (is_null($key))
            return $this->message;
        return array_keys_value($key, $this->message);
    }

    /**
     * 设置某关健字消息
     * @param string|array $key
     * @param mixed $value
     */
    public function set($key, $value = null) {
        if (empty($value)) {
            $this->add($key);
        } else {
            $this->message[$key] = $value;
        }
        return $this;
    }

    /**
     * 添加全局错误消息
     * @param string $value
     * @return \v\ErrSvc
     */
    public function add($value) {
        if (is_array($value)) {
            $this->message = array_merge($this->message, $value);
        } else {
            if (empty($this->message['*'])) {
                $this->message['*'] = $value;
            } else {
                $this->message['*'] .= ", $value";
            }
        }
        return $this;
    }
    
    /**
     * 是否有某错误
     * @param string $key
     * @return boolean
     */
    public function has($key = null) {
        return is_null($key) ? !empty($this->message) : isset($this->message[$key]);
    }

}

?>