<?php
namespace v;
/**
 * 效验服务类
 * 用户的效验类从此继承
 */
class Chk extends Fac {
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
class ChkSvc extends Svc{
    public function valid($value, $rules) {
        // 空值校验
        $message='';
        $required = array_search('*', $rules);
        if ($required === false)
            $required = array_search('required', $rules);
        $message=$this->required($value);
        if ($required !== false) {//存在*号，则value必须存在
            if(!empty($message))
                return $message;
            unset($rules[$required]);
        } else{
            if(!empty($message))
                return;
        }
        $message='';
        // 规范校验
        foreach ($rules as $rule) {
            $str=call_function([$rule, $value], [$this]);
            $message.= empty($str)?'':$str;
        }
        if (!empty($message))
            return $message;
        return;
    }
    

    /**
     * 必填校验
     * @param string $field
     * @return string
     */
    public function required($value) {
        if (empty($value) && (strlen($value) == 0)) {
            return 'Value cannot be empty';
        }
    }

    /**
     * 通过效验
     * @param string $value
     * @return string
     */
    public function pass($value) {
        return;
    }

    /**
     * 正则校验
     * @param string $field
     * @param string $regex
     * @return string
     */
    public function regex($value, $regex) {
        if (!preg_match($regex, $value)) {
            return 'Error in field';
        }
    }

    /**
     * 数字校验
     * @param string $field
     * @return string
     */
    public function digit($value) {
        if (!ctype_digit($value)) {
            return 'Input is not a digit';
        }
    }

    /**
     * 数字与字母校验
     * @param string $field
     * @return string
     */
    public function alnum($value) {
        if (!ctype_alnum($value)) {
            return 'Input can only consist of letters or digits';
        }
    }

    /**
     * 字母校验
     * @param string $field
     * @return string
     */
    public function alpha($value) {
        if (!ctype_alpha($value)) {
            return 'Input can only consist of letters';
        }
    }

    /**
     * 长度校验
     * @param string $value
     * @param int $min
     * @param int $max
     * @return string 
     */
    public function length($value, $min = 0, $max = 0) {
        //$length = strlen(utf8_decode($value));
        $length = mb_strlen($value, 'utf-8');
        if (($length < $min) || (($max > 0) && ($length > $max))) {
            return "Input must be [$min]-[$max] characters";
        }
    }

    /**
     * 数值范围校验
     * @param int|float|string $value
     * @param int|float $min
     * @param int|float $max
     * @return string
     */
    public function between($value, $min = null, $max = null) {
        if ((($min != null) && ($value < $min)) || (($max != null) && ($value > $max))) {
            return "Value must be between [$min]-[$max]";
        }
    }

    /**
     * 等于校验
     * @param string $field
     * @param string $equal
     * @return string
     */
    public function equal($value, $equal) {
        if (!($value == $equal && strlen($value) == strlen($equal))) {
            return 'Both values must be the same';
        }
    }

    /**
     * in_array 校验
     * @param string $value
     * @param array $array
     * @return string 
     */
    public function inArray($value, $array) {
        if (!in_array($value, $array)) {
            return 'Invalid scope';
        }
    }

    /**
     * array_key_exists 校验
     * @param string $value
     * @param array $array
     * @return string 
     */
    public function arrayKeyExists($key, $array) {
        if (!array_key_exists($key, $array)) {
            return 'Invalid scope';
        }
    }

    /**
     * email校验
     * @param string $value
     * @return string
     */
    public function email($value) {
        if (!preg_match('/^\w[-.\w]*@([-a-z0-9]+\.)+[a-z]{2,4}$/i', $value)) {
            return 'Invalid email format';
        }
    }

}

?>