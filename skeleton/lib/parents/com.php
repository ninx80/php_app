<?php
namespace v;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
abstract class Com {

    /**
     * 助手
     * @var array 
     */
    private $helpers = array();

    /**
     * 事件
     * @var array
     */
    private $events = array();

    /**
     * 匿名函数与助手方法添加检查
     * 不允许和当前已和对象方法冲突
     * @param string $name
     * @param mix $value
     */
    public function __set($name, $value) {
        if (!isset($this->$name) && !method_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    /**
     * 调用动态添加的匿名方法与补丁方法
     * @param string $name
     * @param array $args
     * @return mix
     */
    public function __call($name, $args) {
        if (isset($this->$name) && is_callable($this->$name)) {
            return call_user_func_array($this->$name, $args);
        }
        throw new MethodException("Method $name not exists in object");
    }

    /**
     * 载入助手，助手属于单例模式
     * @param string|object $cls 类名
     * @param mixed $other... 助手的参数
     * @return object
     */
    public function helper($cls, $fun = null) {
        if (!is_null($fun)) {
            if (is_callable($fun))
                $this->$cls = $fun;
        } else {
            $clsname = is_string($cls) ? $cls : get_class($cls);
            if (empty($this->helpers[$clsname]) && class_exists($clsname)) { // 载入助手
                if (is_string($cls)) {
                    $args = func_get_args();
                    $args[0] = $this;
                    $rc = new \ReflectionClass($cls);
                    $cls = $rc->newInstanceArgs($args);
                    if (!$cls instanceof Hpr) {
                        throw new ClassException("$cls instance must be instanceof v\Hpr");
                    }
                } else {
                    $rc = new ReflectionObject($cls);
                }
                $this->helpers[$clsname] = $cls;
                foreach ($rc->getMethods() as $mth) {
                    if (substr($mth->name, 0, 2) != '__' && $mth->isPublic() && !$mth->isAbstract() && !method_exists($this, $mth->name)) {
                        $attr = $mth->name;
                        $this->$attr = array($cls, $attr);
                    }
                }
            }
            return $this->helpers[$clsname];
        }
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
/**
 * 框架异常
 */


/**
 * 文件异常
 * 文件不存在时抛出
 */
class FileException extends \Exception {
    
}

/**
 * 类异常
 * 类不存在或类继承错误时抛出
 */
class ClassException extends \Exception {
    
}

/**
 * 方法异常
 * 方法不存在时抛出
 */
class MethodException extends \Exception {
    
}

/**
 * 属性异常
 * 属性错误时抛出
 */
class PropertyException extends \Exception {
    
}