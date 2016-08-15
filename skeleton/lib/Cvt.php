<?php
namespace v;
/*
 * v framework
 * 
 * v框架效验对象
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: Cvt.php 7614 2015-09-16 15:05:19Z wangyong $
 */


class Cvt extends Fac {

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
 * 转换服务类
 * 用户的转换器类从此继承
 */
class CvtSvc extends Svc{
    /**
     * 去除空格与标签
     * @param string $value
     * @param string $allow
     * @return string
     */
    public function striptrim($value, $allow = null) {
        $value = strip_tags($value, $allow);
        return htmlentities(trim($value), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
    }

    /**
     * 格式化成逗号分隔
     * @param string $value
     * @return string
     */
    public function commasplit($value, $fix = false) {
        $fix = ($fix ? ',' : '');
        if (is_string($value))
            return $fix . strtr($value, array(';' => ',', ' ' => '')) . $fix;
        return $fix . implode(',', $value) . $fix;
    }

    /**
     * 以逗号分隔成数组
     * @param string $value
     * @return string
     */
    public function commaarray($value) {
        if (is_string($value)) {
            $value = strtr($value, array(';' => ',', ' ' => ''));
            $value = explode(',', $value);
        }
        return $value;
    }

    /**
     * 浮点数保留小数位
     * @param string $value
     * @param int $precision
     * @return float
     */
    public function floatfix($value, $precision = 2) {
        $value = floatval($value);
        return round($value, $precision);
    }

    /**
     * 不转换数据
     * @param mixed $value
     * @return mixed
     */
    public function passval($value) {
        return $value;
    }
    public function intval($value){
        return (int)$value;
    }

}