<?php
namespace v;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 主要作用是载入类相关配置
 */

abstract class Svc extends Com {

    /**
     * 配置
     * @var array
     */
    protected $configs = [];

    /**
     * 类配置
     * @var array
     */
    protected $myconfs = [];

    /**
     * 载入配置
     */
    public function __construct() {
        // 循环载入所有父类配置，展示不用，没有需求
//        $pconfs = [];
//        $classname = get_called_class();
//        while ($classname && $classname != 'Svc') {
//            $conf_key = strtr($classname, ['Svc' => '', '_Controller' => '', '_Model' => '', '_Job' => '']);
//            array_merge_extend($pconfs, CONF($conf_key), false);
//            $classname = get_parent_class($classname);
//        }
//        array_merge_extend($this->configs, $this->myconfs);
//        array_merge_extend($this->configs, $pconfs);
    }

    /**
     * 获取配置
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key = null, $default = null) {
        if (empty($key))
            return $this->configs;
        if (is_array($key)) {
            $this->configs = array_merge($this->configs, $key);
            return $this;
        }
        return array_keys_value($key, $this->configs, $default);
    }

}