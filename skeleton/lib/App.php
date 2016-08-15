<?php
namespace v;
class App
{   
    private static $controller;        //a方法名即类文件
    private static $method;        //m方法名即view文件名
    private static $controller_file;   //controller类文件路径
    private static $controller_class;  //CONTROLLER类名
    private static $controller_func;   //controller方法
    private static $view;
    private static $path;//去除后缀名后的数据q
    private static $prime_path;//原始请求的数据q
    private static $types=['html','xml','json','css','js','gif','jpg','png','ico','swf','pdf','txt','otf','eot','woff','svg','ttf','csv','file'];
    public static function run(){
        static::parsePath();
        if(CONF('PSEUDO_STATIC')){
            define('PATH',static::routerPreg());
            
        }
        static::router();
        static::runMethod();
    }

    private static function parsePath(){
        $path=trim(__getString('q'),'/');
        static::getType($path);
        if(TYPE=='js' || TYPE=='css'){
            $hash_path=APP_ROOT.'cache/'.md5($path).'.'.TYPE;
            if(file_exists ($hash_path) && CONF('debug')==0)
                Res::apion(file_get_contents($hash_path))->end(200);
            $path=explode(',',$path);
            $path_arr=[];
            $body='';
            foreach($path as $v){
                $v=trim($v,'/');
                $vv=static::routerPreg($v);
                $path_arr[]=['prime_path'=>$v,'path'=>$vv];
                $path1=file_exists(APP_ROOT.$vv)?(APP_ROOT.$vv):( file_exists(APP_PREV_ROOT.$vv) ? (APP_PREV_ROOT.$vv):( file_exists(SYS_ROOT.$vv)?(SYS_ROOT.$vv):'' ) );
                if($path1=='' && CONF('DEBUG')==1){
                    Res::apion('文件'.$v.'不存在')->end(404);
                }else{
                    $pack='pack'.ucfirst(TYPE);
                    $body1=static::$pack($path1);
                    $body.=$body1;
                }
            }
            file_put_contents($hash_path,$body);
            Res::apion($body)->end(200);
        }
        
    }
    private static function getType($path){
        if(defined('TYPE')){
            return TYPE;
        }
        static::$prime_path=$path;
        if(empty($path)){
            define('TYPE','html');
        }else{
            $path =explode('.',$path);
            if(count($path)>1){
                define('TYPE',array_pop($path));
            }else{
                define('TYPE','html');
            }
        }
        static::$path=$path;
        return TYPE;
    }
    private static function routerPreg($path=''){
        $path=$path!=''?$path:implode('.',(static::$path?static::$path:[]));
        $rous=CONF('ROUTE_'.strtoupper(TYPE));
        if(!empty($rous)){
            if(true){//=='json' || TYPE=='html'){//加入jsonp区别
                $keys = preg_replace(array('/\//', '/^/', '/$/'), array('\/', '/^', '$/', '([^\/]+)'), array_keys($rous));
                $rous = preg_replace('/^([^?]+)$/', '$0?', $rous);
                if (!empty($path)) {
                    if (!empty($rous)) {
                        $path = trim(preg_replace($keys, array_values($rous), $path, 1), '?');
                        if ($pos = strpos($path, '?')) {
                            parse_str(substr($path, $pos + 1), $param);
                            $_GET = array_merge($_GET, $param);
                        }
                    }
                }
            }
        }else{
            halt('请求页面不存在');
                    
        }
        return $path;

    }
    /**
    解析路由，且得到相关controller类和方法名
    **/
    private static function router()
    {
        static::$controller = str_replace('-', '/', strtolower(__getString('c', CONF('DEFAULT_CONTROLLER'))));
        static::$method = __getString('m',TYPE=='html'?CONF('DEFAULT_METHOD'):$_SERVER['REQUEST_METHOD']);
        static::$method=in_array(static::$method,['OPTIONS','HEAD','GET','POST','PUT','DELETE','TRACE','CONNECT'])?('res'.ucfirst(strtolower(static::$method))):static::$method;
        define('CONTROLLER',static::$controller);//定义全局变量以便类外使用
        define('METHOD',static::$method);
        static::$controller_file=APP_ROOT.'controller/'.static::$controller.'.php';
        $controller =explode('/',static::$controller);
        static::$controller_class=implode('_',$controller).'_Controller';
        static::$view=APP_ROOT.'view/'.static::$controller.'/'.static::$method.'.html';
    }
    private static function packCss($path){
        return Play::compress(file_get_contents($path));
    }
    private static function packJs($path){
        return JShrink::minify(file_get_contents($path),array('flaggedComments' => false));
    }
    /**
    调用controller类中的方法，并调用压缩、渲染、引入
    **/
    private static function runMethod()
    {
        $act=array();
        if(file_exists(static::$controller_file)){
            include(static::$controller_file);
            $method=static::$method;
            $controller=new static::$controller_class();
            $controller->$method();
            $act=$controller->act;
        }
        else if(CONF('SHOW_CONTROLLER_EXISTS_ERROR')==TRUE){
            halt('页面'.static::$controller_class.'不存在');
        }
        if(TYPE!='html') die;
        play::display(static::$view,$act);
    }
}