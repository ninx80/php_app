<?php
//使用CONF(键名)调用，但是优先取出项目幕目录中的,
$_CONF_SYS=array(
'DEFAULT_APP'=>'',
//默认系统 对应 tpl 一级文件夹名,相当于thinkphp中的分组,暂时不用
'DEFAULT_ACTION'=>'index',
//默认action类 对应 tpl二级文件夹名
'DEFAULT_METHOD'=>'index',
//默认action类中的方法
'DEFAULT_FIRST_INC_TPL'=>[
    ['c'=>'inc','m'=>'top'],
    ['c'=>'inc','m'=>'left']
],//默认首先载入的模板，加载之前，将运行相应的方法
'DEFAULT_LAST_INC_TPL'=>[

    ['c'=>'inc','m'=>'footer'],
],//默认最后载入的模板，加载之前，将运行相应的方法，该方法一般在content之后，其实这个可以去掉，加上符合逻辑些
'DEBUG'    =>1,
//debug调试模式（适用于开发,缓存为最新）normal
'SHOW_ACTION_EXISTS_ERROR'=>true,
//TRUE当false时，允许XXXAction.class.php文件不存在,上线改false
'BASE_CSS'    =>array(),
//array('index.cl','tool')          //默认调入的css，路径在/Public/css中的'.'将替换成'/'
'BASE_JS'       =>array(),
//array('jquery.jquery','tool')   //默认加入的js,路径在/public/js,中的'.'将替换成'/'
'APP_LOAD_PATH'=>array(

    ),
//项目要求的自动载入的文件夹(不包含子文件夹)
'APP_LOAD_FILE'=>array(
    
    ),
//项目要求的自动载入的文件，例如 array(APP_ROOT.'common/love.php')
'PSEUDO_STATIC'=>1,
//是否开启伪静态匹配模式
'STATIC_CACHE'=>1,
//是否使用静态文件模式，不用静态文件的模式框架里还没完全实现
'ROUTE'=>array(
        "([a-z]+\w*)" => 'index.php?c=$1',
        "([a-z]+[a-zA-Z0-9_-]*)/(\w*)" => 'index.php?c=$1&m=$2',
    ),
//为方便伪静态加入路由的正则替换
'DB_CONFIG'=>array(
//数据库配置
        // mongo数据库
        'MongoDBase' => [
            'host' => '127.0.0.1',
            'dbname' => 'test1',
        ],
        // postgresql
        'SqlDBase' => [
            'prefix' => 'pgsql',
            'host' => '192.168.1.18',
            'dbname' => 'sndo',
            'username' => 'postgres',
            'port' => '5432',
            'password' => '123456',
            'options' => []
        ]
    )
);