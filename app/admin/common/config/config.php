<?php
/*该配置会覆盖框架中的配置,可以利用CONF(键名)函数调用,框架里面是这样调用的*/
$_CONF_APP=[
'DEFAULT_APP'=>'admin',
//默认系统 对应 tpl 一级文件夹名,相当于thinkphp中的分组
'DEFAULT_CONTROLLER'=>'index',
//默认CONTROLLER类 对应 tpl二级文件夹名
'DEFAULT_METHOD'=>'index',
//默认controller类中的方法
'AUTO_FIRST_INC_TPL'=>[
    ['c'=>'inc','m'=>'header'],
    ['c'=>'inc','m'=>'left']
],//默认首先载入的模板，加载之前，将运行相应的方法
'AUTO_LAST_INC_TPL'=>[

    ['c'=>'inc','m'=>'footer'],
],//默认最后载入的模板，加载之前，将运行相应的方法，该方法一般在content之后，其实这个可以去掉，加上符合逻辑些
'DEBUG'    =>1,//debug调试模式（适用于开发,缓存为最新）normal

'SHOW_CONTROLLER_EXISTS_ERROR'=>false,//当false时，则允许XXXController.class.php文件不存在,上线改false
    
'BASE_CSS'    =>['/admin/cache/css/bootstrap/bootstrap-theme.min.css','/admin/cache/css/bootstrap.min.css'],//默认加入的css，路径在/public中,先检查skeleton/public下，再检查app/public中

'BASE_JS'       =>['/admin/cache/js/jquery/jquery.min.js','/admin/cache/css/bootstrap/js/bootstrap.min.js','/admin/cache/js/common/td.js'],//默认加入的css，路径在/public中,先检查skeleton/public下，再检查app/public中
//注意：默认加入的css、js文件，始终不会更新缓存，如果要更新，请手动删除或以后加入方法来解决
    
'APP_LOAD_PATH'=>[],//项目默认加入的文件夹设置
'APP_LOAD_FILE'=>[],//项目默认加入的文件夹设置
//项目要求的自动载入的文件，例如 array('common/test/test.php')，但注意：app/lib下的东西不用自动加载，框架采用实时加载的方式
'PSEUDO_STATIC'=>1,
//是否开启伪静态匹配模式
'STATIC_CACHE'=>1,
//是否使用静态文件模式，不用静态文件的模式框架里还没完全实现,暂时无用

'ROUTE_JSON'=>[
        //"admin/(\w+)/id(\w+)" => 'index.php?a=admin&c=$1&id=$2',
        "admin" => 'index.php?',
        "admin/([a-zA-Z0-9_/]+)" => 'index.php?c=$1'
],
//为方便伪静态加入路由的正则替换
'ROUTE_HTML'=>[
    //"admin/(\w+)/id(\w+)" => 'index.php?a=admin&c=$1&id=$2',
    "admin" => 'index.php',
    "admin/([a-zA-Z0-9_/]+)/(\w*)" => 'index.php?&c=$1&m=$2',
],//后缀为html的地址用此类路由，所有end(path)为method
'ROUTE_JS'=>[//利用jsonp来隐藏实际目录，在引入js时候使用cache/js/abc.js实际引入的是public/js/abc.js
    "admin/public/([\w-/.]*)" => 'public/$1',
    "admin/cache/([\w-/.]*)" => 'public/$1',
],
'ROUTE_CSS'=>[//隐藏实际目录,在引入css时候使用cache/js/abc.css实际引入的是public/js/abc.css
    "admin/public/([\w-/.]*)" => 'public/$1',
    "admin/cache/([\w-/.]*)" => 'public/$1',
],
//默认的允许请求的后缀名
'DEFAULT_TYPES'=>['html','xml','json','css','js','gif','jpg','png','ico','swf','pdf','txt','otf','eot','woff','svg','ttf','csv','file'],


'DB_CONFIG'=>[
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
    ]
];