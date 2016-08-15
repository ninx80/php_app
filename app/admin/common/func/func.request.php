<?php
/**
 * 得到已某字符开头的$_REQUEST,digArray，当$thim为false时,
 * array('h_g'=>'1','h_j'=>'2','h'=>'f') h_ false时，得到array('g'=>'1','j'=>'2')
 * 
 **/
function digRequest($filter, $trim = false)
{
	return digArray($_REQUEST, $filter,$trim);//func.array.php里面，以前缀取值[去前缀]
}
function __postString($key,$none_return='')
{
	if(isset($_POST[$key]) && $_POST[$key]!=='')
		return $_POST[$key];
	else
		return $none_return;
}
function getDomain()
{
	$scheme = getValue($_SERVER, 'HTTPS')=='on' ? 'https' : 'http';
	$domain = $scheme.'://';

	$domain .= $_SERVER['SERVER_NAME'];
	if( ($scheme=='http' && getValue($_SERVER, 'SERVER_PORT')!=80) ||
			($scheme=='https' && getValue($_SERVER, 'SERVER_PORT')!=443) )
		$domain .= ':'.$_SERVER['SERVER_PORT'];

	$domain .= '/';
	return $domain;
}

/**
 +-----------------------------------------------------------
 *检测以POST方式传递的数字类型参数
 +-----------------------------------------------------------
 */
function GET_INT($key, $none_return='0')
{
    if(!isset($_GET[$key]))
        return $none_return;
    return intval($_GET[$key]);
}
/**
 +-----------------------------------------------------------
 *检测以POST方式传递的数字类型参数
 +-----------------------------------------------------------
 */
function POST_INT($key, $none_return='0')
{
    if(!isset($_POST[$key]))
        return $none_return;
    return intval($_POST[$key]);
}
/**
 +-----------------------------------------------------------
 *检测以POST或GET方式传递的数字类型参数
 +-----------------------------------------------------------
 */
 function REQUEST_INT($key, $none_return='0')
{
    if(!isset($_REQUEST[$key]))
        return $none_return;
    return intval($_REQUEST[$key]);
}
/**
 +-----------------------------------------------------------
 * 访问远程文件
 +-----------------------------------------------------------
 * @param   string  $url       远程地址
 * @param   int     $timeout   超时时间
 +-----------------------------------------------------------
 * @return mixed
 +-----------------------------------------------------------
 */
function HttpRequest($url,$timeout=3)
{
	$uri = parse_url($url);
	$host = $uri['host'];
	$port = (array_key_exists('port',$uri)?$uri['port']:80);
	$file = getValue($uri, 'path', '/').( array_key_exists('query', $uri)?'?'.$uri['query']:'');
	unset($url,$uri);

	$fp = fsockopen($host,$port, $errno, $errstr, $timeout);
	if(!$fp)
		return '_ERROR_';

	$out = "GET ".$file." HTTP/1.1\r\n";
	$out .= "Host: {$host}\r\n";
	$out .= "Connection: Close\r\n\r\n";
	if(!fwrite($fp, $out))
		return '_ERROR_';

	$part = 'header';
	while(!feof($fp))
	{
		$get = fgets($fp);
		if($part!='body' && $get == "\r\n")
			$part = 'body';
		else
			@$data[$part] .= $get;
	}
	fclose($fp);
	return @$data['body'];
}
/**
 +-----------------------------------------------------------
 * 加密链接
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function EncodeQuery($query)
{
    if(strlen($query)==0)
        return '';

    $tmp_query = preg_replace("/&amp;/i", '&', $query);
    $tmp_query = preg_replace("/&/i", '&amp;', $tmp_query);
    $tmp_array = explode('&amp;', $tmp_query);
    shuffle($tmp_array);
    $tmp_query = implode('&amp;', $tmp_array);
    $tmp_query = random(5). $tmp_query . random(5);
    $tmp_query = rawurlencode($tmp_query);
    $tmp_query = base64_encode($tmp_query);
    $tmp_query = strrev($tmp_query);

    return $tmp_query;
} 

/**
 * @Function POST数组求和
 * @param    $key：POST数组中的KEY
 * @return   int
*/
function POST_ARRAY_SUM($key)
{
	if( isset($key,$_POST[$key]) && is_array($_POST[$key]) )
		return array_sum($_POST[$key]);
	else
		return 0;
}

/**
 +-----------------------------------------------------------
 * 传入字段名构造链接上面的order=xxx
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function GET_ORDER_BY_STRING($fieldName,$link_string)
{
	$flag='';
	if($_SERVER['QUERY_STRING'])
	{
		if(strpos($_SERVER['REQUEST_URI'], 'order='))
		{
			$get_field = explode('|', $_GET['order']);
			if($get_field[0] == $fieldName)
			{
				if($get_field[1] == 'desc')
				{
					$href = str_replace('|desc', '|asc', $_SERVER['REQUEST_URI']);
					$flag = 'down';
				}
				elseif($get_field[1] == 'asc')
				{
					$href = str_replace('|asc', '|desc', $_SERVER['REQUEST_URI']);
					$flag = 'up';
				}
			}
			else
			{
				$href = str_replace('order='.$_GET['order'], 'order='.$fieldName.'|desc', $_SERVER['REQUEST_URI']);
			}
		}
		else
		{
			$href = $_SERVER['REQUEST_URI'].'&order='.$fieldName.'|desc';
		}
	}
	else
	{
		$href = trim($_SERVER['REQUEST_URI'],'?').'?order='.$fieldName.'|desc';
	}

	$href=str_replace('%7C', '|', $href);
	$result='<a class="order'.($flag?' '.$flag:'').'" href="'.$href.'">'.$link_string.'</a>';
	return $result;
}



// 检测表单提交是否为指定的内容
function CHECK_SUBMIT($expectValue,$key='do',$prefix='lswc_')
{
	if(isset($_POST[$key]))
		return $_POST[$key]==($prefix.$expectValue);
	else
		return false;
}

/**
 +-----------------------------------------------------------
 *表单提交标识验证
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function CHECK_FORM_SUBMIT($hidden_do,$key='do')
{
	if(isset($_POST[$key]))
		return $_POST[$key]==(defined('FORM_SUBMIT_PREFIX') && FORM_SUBMIT_PREFIX ? FORM_SUBMIT_PREFIX :'lsmsm_').$hidden_do;
	else
		return false;
}

/**
 +-----------------------------------------------------------
 * 获取$_POST,$_GET,$_SESSION,$_COOKIE中的指定值
 +-----------------------------------------------------------
 * @param   string  $type   类型
 +-----------------------------------------------------------
 * @return  mixed
 +-----------------------------------------------------------
 */
function getgpc($l1='', $l2='r')
{
	switch($l2){
		case 'g':
			$var = &$_GET;
			break;
		case 'p':
			$var = &$_POST;
			break;
		case 'r':
			$var = &$_REQUEST;
			break;
		case 'c':
			$var = &$_COOKIE;
			break;
		case 's':
			$var = &$_SESSION;
			break;
	}
	if(!$l1)
		return $var;
	return (isset($var[$l1]) && $var[$l1]!='') ? $var[$l1] : NULL;
}

/**
 +-----------------------------------------------------------
 * 去掉$_POST,$_GET,$_REQUST中的重复值,这个函数是否有问题，$_POST等中怎么会有key一样,value一样的值
 +-----------------------------------------------------------
 * @param   string  $type   类型
 +-----------------------------------------------------------
 * @return  array
 +-----------------------------------------------------------
 */
function GetAllRequest($type='get')
{
	$result=array();
	if($type=='get')
	{
		foreach($_GET as $k=>$v)
			$result[$k]=$v;
	}
	elseif($type=='post')
	{
		foreach($_POST as $k=>$v)
			$result[$k]=$v;
	}
	elseif($type=='request')
	{
		foreach($_REQUEST as $k=>$v)
			$result[$k]=$v;
	}
	return $result;
}

/**
 +-----------------------------------------------------------
 *检测并获取查询值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function CHECK_QUERY_STRING($value,$key='do',$prefix='')
{
	if(isset($_GET[$key]))
		return $_GET[$key]==($prefix.$value);
	else
		return false;
}

/**
 +-----------------------------------------------------------
 *检测GET方法中是否有指定的INT搜索值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return bool
 +-----------------------------------------------------------
 */
function CHECK_GET_INT($key)
{
	return isset($_GET[$key]) && intval($_GET[$key]);
}
function referer(){

    return $_SERVER['HTTP_REFERER'];

}