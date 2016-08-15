<?php
function array_set_key($key, &$array) {
    $data = array();
    foreach ($array as $row) {
        $data[$row[$key]] = $row;
    }
    return $data;
}
function RESET_APP_ROOT($APP_ROOT){
    $path=trim(__getString('q',''),'/');
    if(empty($path)){
        define('APP_ROOT',$APP_ROOT);
        define('APP','');
        define('Q','');
    }else{
        $path=explode('/',$path);
        if(count($path)>0 && strtolower($path[0])!='cache' && is_dir($APP_ROOT.$path[0])){
            define('APP_ROOT',$APP_ROOT.$path[0].'/');
            define('APP',$path[0]);
            array_shift($path);
            define('Q',implode('/',$path));
        }else{
            define('APP_ROOT',$APP_ROOT);
            define('APP','');
            define('Q',implode('/',$path));
        }
    }
}
function CONF($name=null,$value=null){
    static $_config = array();
    if(is_array($name))//设置$_config
        return $_config = array_merge_append($_config, array_change_key_case($name));//全赋值
    if(is_null($name))//conf(),返回所有配置文件
        return $_config;
    if(is_string($name)){
        if(!strpos($name,'.')){
            $name = strtolower($name);
            if($value===null)
                return isset($_config[$name])? $_config[$name]: [];
            $_config[$name] = $value;
            return $value;
        }//赋值和取值conf('a','b'),conf('a');
        else
        {
            $name = explode('.',$name);
            $name[0]   = strtolower($name[0]);
            if($value===null)
                return isset($_config[$name[0]][$name[1]])
    					? $_config[$name[0]][$name[1]]
    					: [];
            $_config[$name[0]][$name[1]] = $value;
            return $value;
        }//赋值和取值2维的,赋值conf('a.b','ccd'))，取值conf('a.b')
    }elseif($name===false)//false清空
        return $_config = [];//清空 
    

    return null;
}
/**
 +-----------------------------------------------------------
 * 合并数组
 * 如果值是数组，则合在一起
 * 如果不是，则后面的替换前面的
 +-----------------------------------------------------------
 * @param	array     $a  数组1
 * @param	array     $b  数组2
 +-----------------------------------------------------------
 * @return	array
 +-----------------------------------------------------------
 */
function array_merge_append($a, $b)
{
    $tmp = array();
	foreach($b as $k=>$v)
        if(!is_int($k))	// 如果不数字键名,循环进行处理
        {
            if(key_exists($k, $a) && is_array($v))
                $a[$k] = array_merge_append($a[$k], $b[$k]);
            else
                $a[$k] = $b[$k];
        }
        else			//  数字键名
        {
        	$tmp[] = $v;
        }

	$result = array_merge($tmp, $a);

    return $result;
}
/**
 +-----------------------------------------------------------
 * 显示错误
 +-----------------------------------------------------------
 * @param	string    $label  标题
 * @param	string    $msg    信息
 +-----------------------------------------------------------
 * @return	void
 +-----------------------------------------------------------
 */
function halt($msg='错误')
{
    $msg .= CONF('DEBUG') ? "<hr>追踪信息\r\n".trace() : '';
    $output = '<fieldset style="font-size:20px;padding:0;line-height:25px;word-wrap:break-word;border:1px solid #0D65A5;"><legend style="text-align:center;font-size:12px;letter-spacing: 2px;font-weight: bolder;padding:2px 5px;margin-left:10px;border:1px solid #0D65A5;"></legend><pre style="padding:0 20px;">'. $msg . '</pre></fieldset>';
    die($output);
}

/**
 +-----------------------------------------------------------
 * 追踪信息
 +-----------------------------------------------------------
 * @param	int   $peel   忽略的追踪层数
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function trace($peel=1)
{
    $trace_arr=debug_backtrace();
    $trace = '';
    for($i=$peel,$len=count($trace_arr); $i<$len; $i++)
    {
        $j=$i%2;
        $str=(isset($trace_arr[$i]['class'])?('class:'.$trace_arr[$i]['class'].' '):'').(isset($trace_arr[$i]['function'])?('function:'.$trace_arr[$i]['function']):'').(isset($trace_arr[$i]['file'])? (' in <b>'.$trace_arr[$i]['file'].'</b> line '.$trace_arr[$i]['line'])  :'');
        $trace.='#'.$i.' <label >'.$str.'</label><br/>';
    }
    return $trace;
}

/**
* 实例化action文件夹下的类,C('index/a/b') controller/index/a/b.php
*/
function C($str)
{
    static $_controller=array(); 
    $file =APP_ROOT.'controller/'.strtolower($str).'.php';
    $uid=md5($file);
    if(array_key_exists($uid,$_controller))
        return $_controller[$uid];
    $file_arr=explode('/',$str);
    $controller_class=implode('_',$file_arr).'_Controller';
    if(is_file($file)){         
        require_once($file);
    }
    else
        halt($controller_class.'类不存在');
    $_controller[$uid]=new $controller_class();
    return  $_controller[$uid];
}
/**
* 实例化model文件夹下的类,D('index.a.b') model/index/a/bModel.class.php
*/
function D($str)
{
    static $_dmodel=array(); 
    $file=APP_PREV_ROOT.'model/'.$str.'.php';
    $uid=md5($file);
    if(array_key_exists($uid,$_dmodel))
        return $_dmodel[$uid];
    $file_arr=explode('/',$str);
    $model_class=implode('_',$file_arr).'_Model';
    if(is_file($file)){         
        require_once($file);
    }
    else
        halt('文件'.$file.'不存在');
    $_dmodel[$uid]=new $model_class();
    return $_dmodel[$uid];
}
function M($tablename='', $prefix='',$connection='')//实例化Model类,M方法一般要传tablename，如果传空，则连贯方法无法使用，基本上只能用query和excute
{
    static $_model=array(); 
    $params=func_get_args();
    $uid =md5(json_encode($params));
    if(array_key_exists($uid,$_model))
        return $_model[$uid];
    $_model[$uid]=new Model($tablename,$prefix,$connection);
    return  $_model[$uid];
}
/**
* 实例化common/class下文件夹下的类,C('index.a.b',$abc) common/class/index/a/b.php d的b类，参数是 $abc
*/
function O($classname)
{
    static $_class=array();
    $params=func_get_args();
    array_shift($params);
    $classname=str_replace('.','/',$classname);
    $file=APP_ROOT.'common/class/'.str_replace('.', '/', $classname).'.php';
    $filename=explode('/',$classname);
    $classname=strpos($classname,'/') ? end($filename):$classname;
    $uid=md5($file.json_encode($params));
    if(array_key_exists($uid,$_class))
        return $_class[$uid];
    if(is_file($file))
        include_once($file);
    else
        halt('文件'.$file.'不存在');
    if(empty($params))
    {
        $_class[$uid]=new $classname();
        return $_class[$uid];
    }
    $params_str='';
    for($i=0,$len=count($params); $i<$len; $i++)
                $params_str .= ',$params['.$i.']';
    $params_str=trim($params_str,',');
    $_instance='$_instance=new $classname('.$params_str.');';
    eval($_instance);
    $_class[$uid]=$_instance;
    return $_class[$uid];
}
function instace($type,$params)
{
    
}
function arrToString($arr=array())
{
    if(!is_array($arr))
        return $arr;
    $str='';
    foreach($arr as $key=>$val)
    {
        $str.=','.$key."='".$val."'";
    }
    return substr($str,1);
}
/**
 +-----------------------------------------------------------
 * 获取get传值,如果为空,返回$none_return
 +-----------------------------------------------------------
 */
function __getString($key,$none_return='')
{
	if(isset($_GET[$key]) && $_GET[$key]!=='')
		return addslashes($_GET[$key]);
	else
		return $none_return;
}

function __get($key,$none_return='')
{
    if(empty($key)){
        return $_GET;
    }
	if(isset($_GET[$key]) && $_GET[$key]!=='')
		return addslashes($_GET[$key]);
	else
		return $none_return;
}
function __post($key,$none_return=''){
    if(empty($key)){
        return $_POST;
    }
    if(isset($_POST[$key]) && $_POST[$key]!=='')
		return addslashes($_POST[$key]);
	else
		return $none_return;
}
function __put($key='',$none_return=''){
    parse_str(file_get_contents('php://input'),$put);
    if(empty($key)){
        return $put;
    }
    if(isset($put[$key]) && $put[$key]!=='')
		return addslashes($put[$key]);
	else
		return $none_return;
}
function get_ip()
{
	//不允许使用代理，因为HTTP头中HTTP_X_FORWARDED_FOR可以伪造，产生安全隐患
	/*if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else*/
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$realip = $_SERVER['HTTP_CLIENT_IP'];
	else
		$realip = $_SERVER['REMOTE_ADDR'];
	return $realip; 
}
function CHECK_ARRAY(&$array, $length=1)
{
	return isset($array) && is_array($array) && count($array)>=$length;
}
function save_session($array)
{
    foreach($array as $key=>$value)
    {
        $_SESSION[$key]=$value;
    }
}
function session_exists($key)
{
    return isset($_SESSION[$key]) && $_SESSION[$key]!='';
}
function is_goto_login()
{
    if((!isset($_GET['a']) && !isset($_GET['m'])) || (isset($_GET['a'])&&isset($_GET['a'])&&$_GET['a']==''&& $_GET['m']==''))
    {
        header("Location:/index.php?a=login.login");
    }
    if(!session_exists('username'))
    {
        if($_GET['a']!='login.login')
            header("Location:/index.php?a=login.login");
            
    }
}
function array_merge_extend(&$array, $array1, $override = true) {
    foreach ($array1 as $key => $value) {
        if (isset($array[$key]) && is_array($array[$key]) && is_array($value)) {
            array_merge_extend($array[$key], $value);
        } elseif ($override || empty($array[$key])) {
            $array[$key] = $value;
        }
    }
    return $array;
}
/**
 * 根据字符键获取多维数组中的值
 * 如thisfun('s.2')
 * @param string $key
 * @param string $array
 * @param mixed $def
 * @return mix
 */
function array_keys_value($key, &$array, $default = null) {
    if (strpos($key, '.')) {
        $keys = explode('.', $key);
        $data = $array;
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data = $data[$key];
            } else {
                return $default;
            }
        }
        return $data;
    }
    return isset($array[$key]) && $array[$key] !== '' ? $array[$key] : $default;
}
function call_function($fun, $objs = []) {
    $args = [];
    if (is_string($fun)) {
        $fun = [$fun];
    }
    if (is_array($fun) && !is_object($fun[0])) {
        $args = $fun;
        unset($args[0]);
        if (is_string($fun[0])) {
            $funs = explode(',', strtr($fun[0], [';' => ',', ' ' => '']));
            $fun = $funs[0];
            unset($funs[0]);
            $args = array_merge($args, $funs);
        }
    }
    if (is_string($fun)) {
        foreach ($objs as $obj) {
            if (method_exists($obj, $fun)) {
                $fun = [$obj, $fun];
                break;
            }
        }
    }
    
    return call_user_func_array($fun, $args);
}
function uniqid12($prev_char = '') {
    // 距2015年的微秒, 60年范围内
    $nowmics = explode(' ', microtime());
    $sec = substr('00' . base_convert(substr(($nowmics[1] - 1420041600), -9), 10, 36), -6);
    $msec = substr('00' . base_convert(substr($nowmics[0], 2, -2), 10, 36), -4);
    // 最后一位随机产生
    return $prev_char . $sec . $msec . base_convert(mt_rand(0, 35), 10, 36) . base_convert(mt_rand(0, 35), 10, 36);
}
function isAjax(){//判断该请求是否为ajax
        if(defined('AJAX'))
            return AJAX;
        else if(array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            define('AJAX', true);  
        }else
            define('AJAX', false);
        return AJAX;
}
//test
//function striptrim($value){
//    echo $value;
//}