<?php

/*
 * v framework
 * 
 * 广告主模型
 * 
 * @$Author: liuyang $ 
 * @$Revision: 7720 $
 * @$Date: 2015-09-23 12:01:43 +0800 (周三, 23 9月 2015) $
 * @$Id: AderUser.php 7720 2015-09-23 04:01:43Z liuyang $
 */

class AderUser_Model extends AppModel {

    /**
     * 表名
     * @var string
     */
    protected $table = 'ader.user';

    /*
     * 数据库类型
     */
    protected $dbtype = 'mongodb';

    /**
     * 字段定义
     * @var array
     * access=array(//权限管理
     *      adtype=>array(
     *          word=>文字广告
     *          picture=>图片广告
     *          icon=>桌面图标
     *      ),
     *      directional =>array( //定向投放
     *          type=>广告类型(PC,移动)
     *          terminal=>设备
     *          os=>操作系统  
     *          area=>区域  
     *          keyword=>关键字  
     *          sex=>性别
     *          period=>时段  
     *      )
     * )
     */
    protected $fields = [
        '_id' => [['striptrim']], //id
        'username' => [['striptrim'], ['*', 'length, 2, 32']], // 用户名
        'password' => [['striptrim'], ['*', 'length, 6']], // 密码
        'type' => [['intval'], ['pass']], // 财务对象类别,1;个人，2:企业
        'enable' => [['intval'], ['pass']], // 状态,1;正常，0:锁定
        'access' => [['access'], ['pass']], //权限管理(1:有,2:否) directional:定向投放
        'certify' => [['intval'], ['pass']], // 认证级别,0;未认证，1:普通认证
        'noticenum' => [['intval'], ['pass']], // 未读公告数
        'mobile' => [['striptrim'], ['pass']], //电话
        'qq' => [['striptrim'], ['pass']], //qq
        'email' => [['striptrim'], ['pass']], //邮箱
        'lastlogin' => [['intval'], ['pass']], // 最后登陆时间
        'prevlogin' => [['intval'], ['pass']], // 上一次登陆时间
        'loginnum' => [['intval'], ['pass']], // 登陆次数
        'failnum' => [['intval'], ['pass']], // 失败次数
        'lastfail' => [['intval'], ['pass']], // 最后错误时间
        'loginip' => [['striptrim'], ['pass']], // 登陆IP
        'memo' => [['striptrim'], ['pass']], // 备注
        'addtime' => [['intval'], ['pass']], //新建时间
        'puttime' => [['intval'], ['pass']], //修改时间
    ];

    /**
     * 索引
     * @var array
     */
    protected $indexes = [
        ['username' => -1]
    ];

    /**
     * 配置
     * @var array
     */
    protected $loginconfs = [
        'failnum' => 6, // 允许错误次数
        'failsec' => 600 // 错误持续时间
    ];

    /**
     * 在线用户
     * @var array
     */
    protected $onliner = null;

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
                v\Err::set('username', "该用户名已存在:[{$data['username']}]");
                $this->canop = false;
                return $this;
            }
        }
        return $this;
    }

    /**
     * 效验是否可以登录
     * @param array $data 数据
     * @return \Staff_Model
     */
    public function checkLogin(&$data = null) {
        if (empty($data))
            $data = $this->idata;
        if (empty($data['username'])) {
            v\Err::set('username', '请输入用户名');
            $this->canop = false;
        }
        if (empty($data['password'])) {
            v\Err::set('password', '请输入密码');
            $this->canop = false;
        }
        return $this;
    }

    /**
     * 插入与编辑数据hook
     * @param array $data
     * @return \Staff_Model
     */
    public function hookInsertAndUpdate(&$data) {
        // 密码加密 密码md5(userKey+md5(username + password)) 
        if (!empty($data['password'])) {
            $username = empty($data['username']) ? $this->odata['username'] : $data['username'];
            $password = empty($data['password']) ? $this->odata['password'] : $data['password'];
            $data['password'] = md5($this->hashKey() . md5($username . $password));
        }
    }

    /**
     * 插入hook
     * @param array $data
     */
    public function hookInsertDataReady(&$data) {
        parent::hookInsertDataReady($data);
        $this->hookInsertAndUpdate($data);
        if (!isset($data['enable']) || !in_array($data['enable'], array(0, 1)))
            $data['enable'] = 1;
    }

    /**
     * 更新hook
     * @param array $data
     */
    public function hookUpdateDataReady(&$data) {
        parent::hookUpdateDataReady($data);
        $this->hookInsertAndUpdate($data);
    }

    /**
     * 修改成功勾子
     * (如果有修改状态)修改广告计划状态
     * @param type $data
     */
    public function hookUpdateDataSuccess(&$data) {
        if (isset($data['enable']) && $data['enable'] == 0) {
            $items = $this->field('_id')->find();
            $cdata = array('status' => 0);
            v\App::model('AdPlan')->where(['ader_id' => ['$in' => array_column($items, '_id')]])->update($cdata);
        }
    }

    /**
     * 获取userKey //userKey在配置文件中
     * @return string
     */
    public function hashKey() {
        $keys = v\App::config('hashKey');
        return $keys['userKey'];
    }

    /**
     * 用户登陆
     * 密码md5(adminKey+md5(username + password)) 
     * @return boolean
     */
    public function login(&$data = null) {
        if (!empty($data))
            $this->idata = $data;
        if (empty($this->idata))
            return false;
        if (!$this->can())
            return false;
        $username = $this->idata['username'];
        $password = $this->idata['password'];
        $user = $this->where(['username' => $username])->findOne();
        if (empty($user)) {
            // 没有该用户
            v\Err::set('username', "没有该用户");
            return false;
        }

        array_merge_extend($user, ['lastfail' => 0, 'failnum' => 0], false);  // 赋初值
        if ($user['failnum'] >= $this->loginconfs['failnum'] && NOW_TIME - $user['lastfail'] <= $this->loginconfs['failsec']) {
            // 超出允许的次数
            v\Err::set('password', "超过最大失败次数,请稍候登录");
        } elseif ($user['enable'] !== 1) {
            v\Err::set('user', "该用户已锁定,请联系管理员");
        } elseif (md5($this->hashKey() . md5($username . $password)) !== $user['password']) {
            // 密码错误
            v\Err::set('password', "密码错误");
            $udata = ['lastfail' => NOW_TIME];
            if (NOW_TIME - $user['lastfail'] > $this->loginconfs['failsec']) {
                // 过错误持续时间后重置错误次数
                $udata['failnum'] = 1;
            } else {
                $udata['$inc'] = ['failnum' => 1];
            }
            $this->updateByID($user['_id'], $udata);
        } else {
            // 成功记录session，登陆信息与日志
            v\Ses::set('ader_login_id', $user['_id']);
            $udata = [
                'lastlogin' => NOW_TIME,
                'prevlogin' => array_keys_value('prevlogin', $user, NOW_TIME),
                'loginip' => v\Req::ip(),
                '$inc' => ['loginnum' => 1]
            ];
            $this->updateByID($user['_id'], $udata);
            unset($user['password']);
            $this->onliner = $user;
            return true;
        }
        return false;
    }

    /**
     * 登出
     * @return \Staff_Model
     */
    public function logout() {
        v\Ses::set('ader_login_id', 0);
        return $this;
    }

    /**
     * 是否未登陆
     * @return boolean
     */
    public function isGuest() {
        return !v\Ses::true('ader_login_id');
    }

    /**
     * 是否在线
     * @return boolean
     */
    public function isOnline() {
        return !$this->isGuest();
    }

    /**
     * 用户是否具有某权限
     * @param string $permission
     * @return boolean
     */
    public function canAccess($permission) {
        if ($this->isOnline()) {
            $access = $this->onliner('access');
            if (!empty($access)) {
                if ($access == 'all' || strpos(",$access,", $permission) || strpos($permission, "$access")) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 取得在线用户信息
     * @return array
     */
    public function onliner($field = null) {
        if (is_null($this->onliner)) {
            $this->onliner = [];
            $id = v\Ses::get('ader_login_id');
            if (!empty($id)) {
                $this->onliner = $this->where(['_id' => $id])->field(['password' => 0])->findOne();
            }
        }
        return is_null($field) ? $this->onliner : array_keys_value($field, $this->onliner);
    }

}

?>