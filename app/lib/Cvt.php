<?php

/*
 * v framework
 * 
 * 转换对象
 * @$Author: nixun $ 
 * @$Revision: 9114 $
 * @$Date: 2015-12-16 15:49:24 +0800 (周三, 16 12月 2015) $
 * @$Id: Cvt.php 9114 2015-12-16 07:49:24Z nixun $
 */
class Cvt extends v\CvtSvc {

    /**
     * @todo 什么都不做,直接返回
     * @param type $value
     * @return type
     */
    public function ntdo($value) {
        return $value;
    }

    /**
     * @todo 什么都不做,直接返回
     * @param type $value
     * @return type
     */
    public function arr($value) {
        if (empty($value))
            return [];
        if (is_string($value)) {
            $value = preg_split("/[\s,]+/", $value);
        }
        return $value;
    }

    /**
     * @todo 什么都不做,直接返回
     * @param type $value
     * @return type
     */
    public function access($value) {
        if (is_string($value)) {
            $value = preg_split("/[\s,]+/", $value);
        }
        return $this->_recursionToInt($value);
    }

    public function _recursionToInt($array) {
        if (is_array($array)) {
            foreach ($array as $k => $v) {
                $array[$k] = $this->_recursionToInt($v);
            }
        } elseif (preg_match('/^\d+$/', $array)) {
            $array = (int) $array;
        }
        return $array;
    }

}

?>