<?php

/*
 * v framework
 * 
 * 广告模型
 * 
 * @$Author: liuyang $ 
 * @$Revision: 8433 $
 * @$Date: 2015-11-15 17:40:26 +0800 (周日, 15 11月 2015) $
 * @$Id: AdModel.php 8433 2015-11-15 09:40:26Z liuyang $
 */


class AdminUser_Model extends AppModel {

    /**
     * 表名
     * @var string
     */
    protected $table = 'admin.user';

    /*
     * 数据库类型
     */
    protected $dbtype = 'mongodb';

    /**
     * 字段定义
     * @var array
     */
    protected $fields = [
        '_id' => [['striptrim']], //id
        'username' => [['striptrim'], ['*']], // 用户名
        'realname' => [['striptrim'], ['*']], // 姓名
        'password' => [['striptrim'], ['*']], // 密码
        'groupid' => [['striptrim'], ['*']], // 分组id
        'groupname' => [['striptrim'], ['pass']], // 系统组别
        'avatar' => [['striptrim'], ['pass']], // 头像
        'enable' => [['intval'], ['pass']], // 状态,1;正常，0:锁定
        'mobile' => [['striptrim'], ['pass']], //电话
        'qq' => [['striptrim'], ['pass']], //qq
        'email' => [['striptrim'], ['pass']], //email
        'address' => [['striptrim'], ['pass']], //家庭住址
        'lastlogin' => [['intval'], ['pass']], // 最后登陆时间
        'prevlogin' => [['intval'], ['pass']], // 上一次登陆时间
        'loginnum' => [['intval'], ['pass']], // 登陆次数
        'failnum' => [['intval'], ['pass']], // 失败次数
        'lastfail' => [['intval'], ['pass']], // 最后错误时间
        'loginip' => [['striptrim'], ['pass']], // 登陆IP
        'addtime' => [['intval'], ['pass']], //新建时间
        'puttime' => [['intval'], ['pass']], //修改时间
    ];
        /**
     * 检测数据
     * @param array $data 数据
     */
    public function checkUsername(&$data = null) {
        if (empty($data))
            $data = $this->idata;

        // 检测username是否唯一
        if (!empty($data['username'])) {
            $item = $this->where(['username' => $data['username']])->findOne(['_id']);
            if (!empty($item)) {
                //die("该管理员已存在:[{$data['username']}]");
                $this->canop = false;
                return $this;
            }
        }
        return $this;
    }
}

?>