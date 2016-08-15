<?php
/**
 +-----------------------------------------------------------
 * 获取当前页码
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function getCurrentPage()
{
	$page=GET_INT('PG',1);
	if($page<1)
		$page=1;
	return $page;
}

/**
 +-----------------------------------------------------------
 * 获取当前页面大小
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function getPageSize($page_size=20)
{
	if(isset($_GET['PS']) && $_GET['PS'])
		return $_GET['PS'];
	elseif($page_size>0 || $page_size===0)
		return $page_size;
	else
	{
		return $GLOBALS['_PAGER_DEFAULT_SIZE'];
	}
}


//查询数据库中是否存在SNMP的相关表
function SNMPTableValid()
{
	if(!class_exists('Model'))
	{
		include SYS_ROOT.'core/loader.php';
	}
	
	$model_class = new Model();
	
	$snmp_table_arr = $model_class-> tableIsExist('%snmp%');
	
	if(count($snmp_table_arr)>0)
	{
		return true;
	}
	
	$table_prefix = CONF('DB_PREFIX');
	
	//创建view
	$create_table = 'CREATE TABLE `'.$table_prefix.'_snmp_view` (
					   `sv_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT \'AUTO INCREACE ID\',
					   `sv_name` varchar(30) NOT NULL COMMENT \'view别名\',
					   `sv_path` varchar(100) NOT NULL COMMENT \'view路径\',
					   PRIMARY KEY (`sv_id`)
					 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	$model_class -> excute($create_table);
	//添加数据
	$insert_value = 'insert into `'.$table_prefix.'_snmp_view` values
    (1,\'testview\',\'.1.3.6.1.2.1.2;.1.3.6.1.4.1.2021.11;.1.3.6.1.4.1.2021.4;.1.3.6.1.4.1.2021.9\')';
	$model_class -> excute($insert_value);
	
	
	//创建config
	$create_table = 'CREATE TABLE `'.$table_prefix.'_snmp_config` (
					   `sc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					   `sc_name` varchar(50) NOT NULL COMMENT \'配置别名\',
					   `sc_pass` varchar(100) DEFAULT NULL COMMENT \'community,只配置V3版此处可以为空\',
					   `sc_v3protocol` tinyint(50) DEFAULT \'0\' COMMENT \'v3认证：0：MD5，1：SHA\',
					   `sc_v3pass` varchar(100) DEFAULT NULL COMMENT \'v3加密密码\',
					   `sc_v3auth` varchar(100) DEFAULT NULL COMMENT \'v3鉴别密码\',
					   `sc_group` varchar(50) NOT NULL COMMENT \'安全用户组\',
					   `sc_source` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'请求源IP，0：default or ip\',
					   `sc_version` varchar(50) NOT NULL DEFAULT \'v1,v2c\' COMMENT \'snmp 版本\',
					   `sc_context` varchar(50) DEFAULT NULL,
					   `sc_model` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0:any;1:v1;2:v2;3:usm\',
					   `sc_level` int(10) unsigned NOT NULL DEFAULT \'1\' COMMENT \'0:auth;1:noauth;2:priv\',
					   `sc_prefix` varchar(20) NOT NULL DEFAULT \'exact\' COMMENT \'v3使用\',
					   `sc_read` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0:none or sv_id\',
					   `sc_write` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0:none or sv_id\',
					   `sc_notify` int(10) unsigned NOT NULL DEFAULT \'0\' COMMENT \'0:none or sv_id\',
					   `sc_swicher` int(10) unsigned NOT NULL DEFAULT \'1\' COMMENT \'该条配置是否启用。0：未启用，1：启用\',
					   PRIMARY KEY (`sc_id`)
					 ) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
	$model_class -> excute($create_table);
	//添加数据
	$insert_value = 'insert into `'.$table_prefix.'_snmp_config`(sc_id,sc_name,sc_pass,sc_group,sc_read) 
					 values (1,\'test\',\'lonsin\',\'testgroup\',1)';
	$model_class -> excute($insert_value);
	
	return true;
}
/**
 +-----------------------------------------------------------
 * 检测并清理用分隔符分隔的ID
 +-----------------------------------------------------------
 * @param	string	$ids
 * @param	string	$spe	分隔符
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function checkIds($ids, $spe=',')
{
	$ids = explode($spe, $ids);
	foreach($ids as $k=>$id)
	{
		if(!preg_match('/^\d+$/', $id))
			unset($ids[$k]);
	}
	return implode($spe, $ids);
}

/**
 +-----------------------------------------------------------
 * 获取16进制编码
 +-----------------------------------------------------------
 * @param	string    $n
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function mGetHCode($n)
{
	return strtoupper(dechex($n));
}

//格式化文件大小
function GET_PRETTY_FILE_SIZE($size,$unit='byte')
{
	if(!$size)
		return '';

	switch ($unit)
	{
		case 'byte':
			if($size>=1073741824)
				return round(($size/(1024*1024*1024)),2).' GB';
			elseif($size >= 1048576)
		 		return round(($size/(1024*1024)),2).' MB';
			elseif($size >= 1024 && $size<1048576)
				return round(($size/1024),2).' KB';
			else
				return $size.' B';
			break;
		case 'kb':
			if($size>=1073741824)
				return round(($size/(1024*1024*1024)),2).' TB';
			elseif($size >= 1048576)
		 		return round(($size/(1024*1024)),2).' GB';
			elseif($size >= 1024 && $size<1048576)
				return round(($size/1024),2).' MB';
			else
				return $size.' KB';
			break;
		case 'mb':
			if($size >= 1048576)
				return round(($size/(1024*1024)),2).' TB';
			elseif($size>=1024 && $size<1048576)
				return round(($size/1024),2).' GB';
			else
				return $size.' MB';
			break;
		default:
			break;
	}
	return $file_size;
}


/**
 +-----------------------------------------------------------
 * 将数组转换为HTML表格代码
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return	HTML
 +-----------------------------------------------------------
 */
function GetTableRowsFromArray($array,$cols=1,$default_text='',$row='')
{
	if(intval($cols)<1)
		return 'error:table cols must be INTEGER-TYPE';

	if(!is_array($array) || count($array)==0)
		return 'error:table has no data';

	$array_count=count($array);
	$mod = ceil($array_count / $cols)*$cols - $array_count;
	for($i=0;$i<$mod;$i++)
	{
		$array[]=$default_text;
	}
	$row_count=$array_count / $cols;

	if($row > $row_count)
		$row_count=$row;

	$col_width=100 / $cols;

	$str='';
	$tmp=array_chunk($array,$cols);
	for($i=0;$i<$row_count;$i++)
	{
		$str.='<tr class="bgColor'.($i%2).'">'."\n";
		for($j=0;$j<$cols;$j++)
		{
			$data=$tmp[$i][$j];
			$str.="<td width=\"{$col_width}%\">$data</td>\n";
		}
		$str.="</tr>\n\r";
	}
	return $str;
}

/**
 +------------------------------------------
 * 根据传入的数组自动构造选项卡
 +------------------------------------------
 * @param $arr 数组
 +------------------------------------------
 * @return string
 +------------------------------------------
 */
function switcher($arr, $key='', $default='')
{
    $str='';
    if(is_array($arr))
    {
        foreach($arr AS $link => $name)
        {
			$href = Url($link);
			if($key )
				$cls = parseUrl($key,$href) == (parseUrl($key)?parseUrl($key):$default) ? ' class="ing"':'';
			else
			{
				$parts = Url($link,1);
				$cls= (ACTION==$parts['action'] && MODULE==$parts['module']) ? ' class="ing"' : '';
			}
            $str .= '<li><a href="###" onclick="Go(\'/?'.$href.'\')"'.$cls.'>'.$name.'</a></li>'."\t\n";
        }
        
        return $str;
    }
    die('bad arg ['.$arr.']');
}

/*设置超级全局变量*/
function SupperGlobals()
{
    
  if(!key_exists('expire_date', $GLOBALS))
  {
	  $info = getLicence();
     $GLOBALS['username'] = $info['username'];
     $GLOBALS['maxuser']=$info['maxusernum'];
	 $GLOBALS['expire_date']=$info['expire_date'];
	 $GLOBALS['expire_status']=$info['expire_stauts'];
  }
}

/**
 +-----------------------------------------------------------
 *显示帮助提示信息
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return bool
 +-----------------------------------------------------------
 */
function SHOW_HELP_MESSAGE($array)
{
    $str='';
	if(is_array($array) && count($array)>0)
	{
		foreach($array as $message)
		{
			$str.= "<li>{$message}</li>\n";
		}
	}
    return $str;
}


/**
 +-----------------------------------------------------------
 *清除搜索条件
 +-----------------------------------------------------------
 * @param 需要过滤的值
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function CLEAR_SEARCH_CONDITION($filter=array())
{
	$search='';
	if(is_array($filter) && count($filter)>0)
	{
		foreach($filter as $k=> $v)
		{
			if($_GET[$k] && !$v)
			{
				$search.=$k.'='.urlencode($_GET[$k]);
				$search.='&';
			}
			else if($_GET[$k] && $v)
			{
				$search.=$k.'='.$v.'&';
			}
		}
		$search=substr($search,0,-1);
	}

	return '<a href="###" id="condition_clear_all" onclick="Go(\'/?m='.MODULE.'&a='.ACTION.'\')" title="全部清除">×</a>';
}

/**
 +-----------------------------------------------------------
 * 比较两个值的大小
 +-----------------------------------------------------------
 * @param	$v1,$v2 需要比较的两个数值
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function COMPARE_VALUE($v1,$v2)
{
	if(intval($v1)==intval($v2)) 		return 0;
	elseif(intval($v1)>intval($v2)) 	return 1;
	else 				return -1;
}

/**
 +-----------------------------------------------------------
 * 获取证书信息
 +-----------------------------------------------------------
 * @param 	string	$file	证书地址
 +-----------------------------------------------------------
 * @return	array
 +-----------------------------------------------------------
 */
function getLicInfo($key='')
{
    defined('LIC_INFO') || define('LIC_INFO',MYSYS_PATH.'config/var/licinfo.xml');
    
	if(!file_exists(LIC_INFO))
		return false;
		
	static $Info;
	if(!$Info)
		$Info = INST('xmlparser')->parseFile(LIC_INFO, 'licinfo');

	return $key ? getValue($Info, $key) : $Info;
}

/**
*+----------------------------------------------
*  获取licence 信息
+----------------------------------------------
*/
function getLicence()
{
    static $array;
    if($array) {
        return $array;
    }
    $array || $array=getLicInfo();

	if(empty($array))
	{
	    return array('username'=>'?',
                    'maxusernum'=>'?',
                    'expire_date'=>'?',
                    'expire_stauts'=>'{"result":3,"d":0}');
	}
	
    $array['expire_stauts'] = '{"result":3,"d":0}';
	if($array['begintime']=='0' && $array['validtime']=='0')
    {
		$array['expire_date']='';               
    }
	elseif($array['validtime']=='0')
    {
		$array['expire_date']='(永久有效)';
        $array['expire_stauts'] = '{"result":2,"d":0}';
    }
	else
	{
		$ts	= intval($array['begintime']+$array['validtime']);
		$array['expire_date']	= date('Y-m-d',$ts)
								.(
									$ts<time() || intval($array['begintime'])>time()
									?'(已过期)'
									:(floor(($ts-time())/86400)==0
										?'(今天到期)'
										:'(剩余'.floor(($ts-time())/86400).'天)'
									)
								);  
        $array['expire_stauts'] =   $ts<time() || intval($array['begintime'])>time()
									?'{"result":0,"d":-1}'
									:(floor(($ts-time())/86400)>5
										?'{"result":2,"d":0}'
										:'{"result":0,"d":'.floor(($ts-time())/86400).'}'
									);      
	}
		
	if($array['expire_date']=='' || $array['username'] == '')
    {
		$array['expire_date']='(未授权)';
    }
	
	if($array['username']=='0')
    {
		$array['username']='(未授权)';
    }
    $array['modules']=getLicModules($array['module']);
	
	return $array;
}


/**
 +-----------------------------------------------------------
 * 获取当前可用模块
 +-----------------------------------------------------------
 * @return	bool
 +-----------------------------------------------------------
 */
function getLicModules($licModule=true, $getType='string')
{
    static $MODULES;
    if($MODULES) {
        return $getType=='string' ? implode(', ',$MODULES) : $MODULES;
    }
    if($licModule===true) {
        $licModule=getLicInfo('module');
    }
    if(!$licModule) {
        return '';
    }
    $licModule=explode(',', $licModule);

    if(!file_exists(APP_ROOT.'define/DEFINE_MODULE.php')) {
        return '';
    }
    $MODULES || $MODULES=include(APP_ROOT.'define/DEFINE_MODULE.php');

    if(CHECK_ARRAY($MODULES))
    foreach($MODULES as $module=>&$name) {
        if('base'!=$module && 'member'!=$module && !in_array('mobile_'.$module, $licModule)) {
            unset($MODULES[$module]);
        }
    }

    return $getType=='string' ? implode(', ',$MODULES) : $MODULES;
}
/**
 +-----------------------------------------------------------
 * 检查证书是否到期或不存在
 +-----------------------------------------------------------
 * @return	bool
 +-----------------------------------------------------------
 */
function checkLicNeed()
{
    defined('LIC_NEED') || define('LIC_NEED', WEB_ROOT.'licneed');
	return file_exists(LIC_NEED);
}

/**
 +-----------------------------------------------------------
 * 根据表名称取其主键或其他ID
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function GET_TABLE_KEY_BY_NAME($tableName,$pfx=false,$cm=false)
{
	$tableName=str_replace('lssec_','tbl_',$tableName);
        
    //判断_SEC_DATABASE_TABLES_NAME是否存在索引prefix
    if(isset($GLOBALS['_SEC_DATABASE_TABLES_NAME'][$tableName]['prefix']))
    {
        $prefix = $GLOBALS['_SEC_DATABASE_TABLES_NAME'][$tableName]['prefix'];
    }
    else
    {
	   $prefix = $GLOBALS['_SEC_DATABASE_TABLES_NAME'][$tableName][3];
    }
    
    if($cm)
	   return $pfx?$prefix:'id';
    else
       return $pfx?$prefix:$prefix.'id';
}

/**
 +-----------------------------------------------------------
 * 异步访问本地网页
 +-----------------------------------------------------------
 * @param   string  $url       要访问的地址，不要域名
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function AsynCall($url)
{
    $uri = parse_url($url);
	$host = $uri['host'];
	$port = (key_exists('port',$uri)?$uri['port']:80);
	$file = getValue($uri, 'path', '/').( key_exists('query', $uri)?'?'.$uri['query']:'');
	unset($url,$uri);
	
    $fp = fsockopen($host, $port, $errno, $errstr, 5);
    if(!$fp)
        return;
    
    $out = "GET /".$file." HTTP/1.1\r\n";
    $out .= "Host: {$host}\r\n";
    $out .= "Connection: Close\r\n";
    if(isset($_SERVER['HTTP_COOKIE']))
    	$out .= "Cookie: ".$_SERVER['HTTP_COOKIE']."\r\n";
    $out .= "\r\n";
    fwrite($fp, $out);
    fclose($fp);
    return;
}
/**
 +-----------------------------------------------------------
 * 与嵌入式端的套接字通信
 +-----------------------------------------------------------
 * @param   integer $msgId 消息号
 * @param   array   $param 传递的参数
 * @param   integer $port  服务器端口号
 * @param   interger $timeout 超时时间
 * @param   string  $ip    服务器ip地址
 +-----------------------------------------------------------
 * @return mixed
 +-----------------------------------------------------------
 */
function getMessage($msgId=0, $param = array(), $port = 0, $ip = '',$timeout = 10)
{
    if(!$msgId)
        return false;
    $socket = INST('xsocket', 'tcp',
        !empty($ip) ? $ip : G('_SOCKET_IP'),
            !empty($port) ? $port : G('_SOCKET_PORT'));
    
    #构造参数
    $msg_struct     = G('_MSG_STRUCT.'.$msgId);
    $param          = array_merge(is_array($msg_struct) ? $msg_struct : array(), $param);
    $param['MsgID'] = $msgId;
    
    #发送消息
    $result = $socket->set_timeout(intval($timeout))->show_error(false)->send($param);
    
    $msg_debug_file = '/tmp/msg_debug';
    if(file_exists($msg_debug_file))
    {
        $content  = mFormatTime().json_encode($param)."\r\n";
        
        $content .= mFormatTime() . ' MsgID:' . $msgId . ' Result:' . ($result ? json_encode($result) : '-1');
        $content .= "  \r\n";
        @file_write($msg_debug_file, $content, FILE_APPEND);
    }
    
    if($socket->get_errno() > 0)
        return false;
    
    if(!empty($result) && $result['MsgID'] == $msgId)
    {
        if($result['Result'] !== 0)
            return false;

		unset($result['MsgID'], $result['Result']);
		if(empty($result))
			return true;

		return $result;
    }
    return false;
}

/**
 +-----------------------------------------------------------
 * 根据传入的数值获取%比字符串
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function GetPercentFromNum($num,$all,$precision=4)
{
	if($all>0 && is_numeric($num) && is_numeric($all))
		return (round($num/$all,$precision)*100).'%';
	else
		return '0%';
}

/**
 +-----------------------------------------------------------
 * 格式化表头排序
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function HANDLE_ORDER_STRING($default = '', $array = array())
{
	if(isset($_GET['order']) && $_GET['order'])
	{
		$orderarr = explode('|', $_GET['order']);
		if(array_key_exists($orderarr[0], $array))
		{
			$result = (' ORDER BY '.$array[$orderarr[0]].' '.$orderarr[1]);
		}
		else
		{
			$result = (' ORDER BY '.$orderarr[0].' '.$orderarr[1]);
		}
	}
	else
	{
		$result = $default ? ' ORDER BY '.$default : '';
	}
	return $result;
}

/**
 +-----------------------------------------------------------
 * 检查用户是否从管理口登录
 +-----------------------------------------------------------
 * @param	string	$ip	IP
 +-----------------------------------------------------------
 * @return	bool
 +-----------------------------------------------------------
 */
function CHECK_CONSOLE_LOGIN($ip=null)
{
	// 如果检测到remote_ctrl文件，认为当前登录是管理员行为，放行
	if(file_exists('/tmp/remote_ctrl'))
		return true;
		
	$ip || $ip=get_ip();
	if($ip=='127.0.0.1' || strpos($ip,'192.168.10.')!==FALSE)
		return true;

	return false;
}


function __LOAD__($file,$type)
{
	static $queue=array();
	if(!$file)		return false;

	$__d=$type
		? APP_ROOT.$type.'/'
		: '';

	if( CHECK_ARRAY($file) || ( strpos($file,GLUE)>0 && ($file=explode(GLUE,$file)) && CHECK_ARRAY($file) ) )
	{
		foreach($file as $_file)
		{
			$__s=$__d.__ck_file_src__($_file,'').'.php';
			if( $_file && file_exists($__s) && !in_array($__s,$queue) )
			{
				include($__s);
				$queue[]=$__s;
			}
		}
	}
	else
	{
			$__s=$__d.__ck_file_src__($file,'').'.php';
			if( file_exists($__s) && !in_array($__s,$queue) )
			{
				include($__s);
				$queue[]=$__s;
			}
	}
	return true;
}
function LOAD($file,$module='')
{
	$module || $module=MODULE;
	return __LOAD__($file,CONF('APP_DOO').$module);
}
function LOAD_INC($file)
{
	return __LOAD__($file,'inc');
}
function LOAD_BZO($file)
{
	return __LOAD__($file,'bzo');
}
function LOAD_DAO($file)
{
	return __LOAD__($file,'dao');
}
function LOAD_INC_CLASS($file)
{
	return __LOAD__($file,'inc/class');
}

function __ck_file_src__($f,$fix)
{
	return ($f[0]=='.' && $f[1]=='/')
			? substr($f,2)
			: (($f[0]=='.' && $f[1]=='.' && $f[2]=='/')
				? $f
				: $fix.$f);
}

function CHECK_FILE_PROTECTED($fp)
{
	$c=F()->file_read($fp,100);
	if(!$c)
		return false;
	return strpos($c,LSWC_FILE_PROTECTED_TEXT)>0;
}

function getMaskBit($mask)
{
 	$mask = decbin(ip2long($mask));
 	return ($pos=strpos($mask, '0'))!==false?$pos:32;
}
 

/**
+-----------------------------------------------------------
* 将二进制数据转换为可见的字符串
+-----------------------------------------------------------
* @param	string	$bin	二进制数据
* @param	int		$format	输出的格式	2,8,10,16
+-----------------------------------------------------------
* @return	bit
+-----------------------------------------------------------
*/
function bin2string($bin, $format = 2)
{
    $bin = bin2hex($bin);
    if($format == 2)
        $bin = sprintf('%0'.(4 * strlen($bin)).'b', hexdec($bin));
    elseif($format == 8)
        $bin = (string )decoct(hexdec($bin));
    elseif($format == 10)
        $bin = (string )hexdec($bin);
    else
        $bin = strtoupper($bin);
    return $bin;
}

/**
+-----------------------------------------------------------
* 读取二进制数据对应位的值
* 注意：二进制数据，非二进制字符串
+-----------------------------------------------------------
* @param	string	$bin	二进制数据
* @param	int		$bit	要读取的位
+-----------------------------------------------------------
* @return	bit
+-----------------------------------------------------------
*/
function getbit($bin, $bit)
{
    return ( hexdec(bin2hex($bin)) & (0 + ('0x'.dechex(1 << $bit))))?'1':'0';
}

/**
+-----------------------------------------------------------
* 设置二进制数据对应位的值
* 注意：二进制数据，非二进制字符串
+-----------------------------------------------------------
* @param	string	$bin	二进制数据
* @param	int		$bit	要设置的位
* @param	bit		$value	值	0或1
+-----------------------------------------------------------
* @return	string
+-----------------------------------------------------------
*/
function setbit($bin, $bit, $value)
{
	$value = $value ? 1 : 0;
	if(getbit($bin, $bit) == $value)
		return $bin;
		
    $bin = hexdec(bin2hex($bin));
    if($value)
		$bin += '0x'.dechex(1 << $bit);
	else
		$bin = $bin^(0+('0x'.dechex(1<<($bit-1))));
	
	$bin = dechex($bin);
	if(strlen($bin)%2==1)
		$bin ='0'.$bin;
	$bin = str_split($bin, 2);
	foreach($bin as &$b)
		$b = hexdec($b);
	array_unshift($bin, 'c*' );
	return call_user_func_array('pack', $bin);
}

/**
+-----------------------------------------------------------
* 交换两个变量的值
+-----------------------------------------------------------
* @param	mixed	$a
* @param	mixed	$b
+-----------------------------------------------------------
* @return	mixed
+-----------------------------------------------------------
*/
function swap(&$a, &$b)
{
	$tmp = $a;
	$a = $b;
	$b = $tmp;
}

//根据depondonCheckbox生成指定列数量的表格行
function GetTableRowsFromDepondonCheckboxStr(&$str,$cols=3)
{
	$str = preg_replace('/<div class="itemList"[^><]*>/i','',$str);
	$str = str_replace('</div>','',$str);
	$str = str_replace('</label>','</label>||',$str);
	$arr = explode('||',$str);
	array_pop($arr);
	$len 		= count($arr);
	$rows 		= $len / $cols;
	$rows 		= $rows<1 ? 1 : $rows;
	$tdWidth 	= sprintf("%.2f",1/$cols) * 100;
	$k = 0;$i = 0;
	$str = '';
	
	if (CHECK_ARRAY($arr))
	for($i;$i<$rows;$i++)
	{
		$str.='<tr class="bgColor_'.($i % 2).'">'."\n";
		for($j = 0;$j<$cols;$j++)
		{
			$str.= '<td width="'.$tdWidth.'%">'.(isset($arr[$k])?$arr[$k]:'').'</td>'."\n";
			$k++;
		}
		$str.='</tr>'."\n";
	}
}

/**
 * 将十进制按位读取
 * @param int $num
 * @return array
 */
function bit2array($num)
{
	$bin = decbin($num);
	$len = strlen($bin);
	$tmp = array();
	for($i=0; $i<$len; $i++)
	{
		if(substr($bin, '-'.($i+1), 1)==1)
			$tmp[] = 1<<$i;
	}
	return $tmp;
}

function genLicense(&$lic, $licneedPath='')
{
	$licneedPath && $lic['status']=(!file_exists($licneedPath));
	if($lic['begintime']=='0' && $lic['validtime']=='0')
		$lic['expire_date']='(未获得授权)';
	elseif($lic['validtime']=='0')
		$lic['expire_date']='(永久有效)';
	else
		$lic['expire_date']='有效期到 '.date('Y-m-d',intval($lic['begintime']+$lic['validtime']));

	$lic['username'] || $lic['username']='(未获得授权)';
}

/**
 * @todo    创建Nav/help/search
 * @param   array   $arr 
 * @return  void
 * 
 * @abstract	2012.10.13 very80, from KNSManage, by ZMB
 */
function buildNavHelpSearch(&$arr)
{
    $searchStr = $helpStr ='';
    $str  = '<div id="page_title">
		<div id="title">'
		.(isset($arr['navi'])?$arr['navi']:'')
		.'</div>
		<div id="search_help">';

    if(isset($arr['search']) && $arr['search']!='')
    {
        $str .= '<a href="###" id="search_info">搜索</a>';
        $searchStr='<div id="search_from">'.$arr['search'].'</div>';
    }

    if(isset($arr['help']) && $arr['help']!='')
    {
        $str .= '<a href="###" id="help_info" title="点击查看帮助">帮助</a>';
        $helpStr ='<div class="help_box">
                        <div class="title">帮助信息</div>
                        <div class="close"><a href="###" id="close_help"></a></div>
                    	<ul class="help_con">'.$arr['help'].'</ul>
                    </div>';
    }

    $str .= '</div>
	<div class="clear"></div>
	</div>'.$searchStr.$helpStr;

    unset($arr['search']);

    $arr['nav_help_search']=$str;
}
