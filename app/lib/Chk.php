<?php
class Chk extends v\ChkSvc{
    public function required($value) {
        //if (empty($value) && (is_string($value) && strlen($value) == 0)) {
        if (is_array($value) || (empty($value) && strlen($value) == 0)) {
            return '不能为空';
        }
    }
}
