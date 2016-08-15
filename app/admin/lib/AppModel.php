<?php

/*
 * v framework
 * 
 * 控制器对象
 * @$Author: liuyang $ 
 * @$Revision: 7110 $
 * @$Date: 2015-08-21 10:27:13 +0800 (周五, 21 8月 2015) $
 * @$Id: Model.php 7110 2015-08-21 02:27:13Z liuyang $
 */


/**
 * 控制器类
 */
class AppModel extends v\DBaseModel {

    /**
     * 数据库类型
     * @var string
     */
    protected $dbtype = 'mongodb';

    public function __construct() {
        $this->addHook(self::HOOK_INSERT_DATA_READY, [$this, 'hookInsertDataReady']);
        $this->addHook(self::HOOK_UPDATE_DATA_READY, [$this, 'hookUpdateDataReady']);
        $this->addHook(self::HOOK_INSERT_DATA_SUCCESS, [$this, 'hookInsertDataSuccess']);
        $this->addHook(self::HOOK_UPDATE_DATA_SUCCESS, [$this, 'hookUpdateDataSuccess']);
        $this->addHook(self::HOOK_REMOVE_DATA_SUCCESS, [$this, 'hookRemoveDataSuccess']);
        
        parent::__construct();
    }

    /**
     * 设置默认数值
     * @param array $data
     */
    protected function hookInsertDataReady(&$data) {
        $fields = $this->fields();
        if (in_array('addtime', $fields) && empty($data['addtime'])) {
            $data['addtime'] = time();
        }
        if (in_array('puttime', $fields) && empty($data['puttime'])) {
            $data['puttime'] = time();
        }
    }
    /**
     * 设置默认数值
     * @param array $data
     */
    protected function hookUpdateDataReady(&$data) {
        $fields = $this->fields();
        if (in_array('puttime', $fields) && empty($data['puttime'])) {
            $data['puttime'] = time();
        }
        //不能更新_id,addtime
        unset($data['_id'], $data['addtime']);
    }


    /**
     * 插入成功勾子
     * @param type $data
     */
    public function hookInsertDataSuccess(&$data) {
        
    }

    /**
     * 修改成功勾子
     * @param type $data
     */
    public function hookUpdateDataSuccess(&$data) {
        
    }

    /**
     * 删除成功勾子
     * @param type $data
     */
    public function hookRemoveDataSuccess(&$data) {
        
    }

}

?>