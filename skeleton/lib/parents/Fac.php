<?php
namespace v;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
abstract class Fac {
    /**
     * 服务提供对象名
     * 必须子类定义
     * @var string
     */
    //protected static $objname;

    /**
     * 服务提供对象
     * 必须子类定义
     * @var object
     */
    //protected static $object;

    /**
     * 注入原始类方法与属性
     * @param string $class
     */
//    private static function inject() {
//        $object = static::$object;
//        if ($object instanceof Svc) {
//            $class = get_called_class();
//            $rc = new \ReflectionClass($class);
//            foreach ($rc->getMethods() as $mth) {
//                if (substr($mth->name, 0, 2) != '__' && $mth->isPublic() && !$mth->isAbstract() && !method_exists($object, $mth->name)) {
//                    $attr = $mth->name;
//                    $object->$attr = [$class, $attr];
//                }
//            }
//        }
//    }

    /**
     * 获取单例对象
     * @return object
     */
    public static function object() {
        if (empty(static::$object)) {
            $class_name=get_called_class();//chksvc
            $sys_class_name=$class_name.'Svc';
            if (substr($class_name, 0, 2) == 'v\\') {
                $app_class_name=substr($class_name,1);//chk;
            }else
                $app_class_name=$class_name;
            if(class_exists($app_class_name)){
                static::$object = new $app_class_name();
            }else{
                static::$object = new $sys_class_name();
            }
        }
        return static::$object;
    }

    /**
     * 魔术调用静态方法
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        $obj = static::object();
        if(method_exists (static::$object,$name)){
            return call_user_func_array([static::$object,$name], $arguments);
        }else{
            halt('无函数 '.$name);
        }
    }

}
