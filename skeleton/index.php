<?php
define('SYS_ROOT', dirname(str_replace('\\', '/', __FILE__)).'/');//框架目录
require_once(SYS_ROOT.'common/func.php');//载入框架所需函数
RESET_APP_ROOT($APP_ROOT);//为了使用于项目分组
define('APP_PREV_ROOT',$APP_ROOT);
unset($APP_ROOT);
session_cache_limiter('public,max-page=10800');
session_start();
session_set_cookie_params((isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time())+900);
require(SYS_ROOT.'common/config/config.php');
require(APP_ROOT.'common/config/config.php');
CONF(array_merge($_CONF_SYS,$_CONF_APP));
CONF('DEBUG')?error_reporting(E_ALL):error_reporting(0);
require(SYS_ROOT.'common/Loader.php');
spl_autoload_register(function($classname) {//类不存在就去app的lib里面找,类名为Test_Test 则会找app/lib/Test/Test.php,在linux下文件区分大小写，所以严格按照大小写编写文件名和类名
    $classname=strtr($classname,['_'=>'/']);
    $sys_file=SYS_ROOT.'lib/'.$classname.'.php';
    $prev_file=APP_PREV_ROOT.'lib/'.$classname.'.php';
    $file = APP_ROOT.'lib/'.$classname.'.php';
    if (is_file($file)) {
        require_once $file;
    }elseif(is_file($prev_file)){
        require_once $prev_file;
    }
});
v\App::run();
/**
该框架执行过程简单的说为：
在不要求伪静态的前提下访问index?a=admin&c=index&m=index
1、首先执行分组为admin中的controller文件夹中的indexActon.class中method--index()
2、生成缓存文件：/admin/cache/hash(地址).tpl.php（这一步主要是渲染和加载默认css js），但当没有tpl/index/index.html文件或为ajax请求时，直接die； 
3、include该缓存文件。
**/

