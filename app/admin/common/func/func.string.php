<?php
/**
 +-----------------------------------------------------------
 * 获取一个随机哈希值
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function getLoginHash()
{
	return md5(mFormatTime(0,"Ymd").mFormatTime(0,'d').'*)*)LoNsIn');
}


/**
 +-----------------------------------------------------------
 * 构建跳转页面
 +-----------------------------------------------------------
 * @param   string  $type       类型
 * @param   string  $message    显示的信息
 * @param   array   $buttons    显示的按钮
 * @param   string  $url        跳转路径
 * @param   int     $Msonds    等待时间
 * @param   string  $target     目标窗口
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function jsTip($type='info',$message='',$buttons=array(),$url='',$Msonds=3,$target='self')
{
	$bts = '';
	if(is_array($buttons) && count($buttons)>0)
	{
		foreach($buttons as $button)
			$bts .= ' '.$button;
	}
	if($bts=='' && !is_null($buttons))
		$bts = FORM()->Button('返回上页',array('onclick'=>'window.history.go(-1);'));
	if($url=='')
		$url = $_SERVER['HTTP_REFERER'];

	$render['icon']		= $GLOBALS['_TIMER_ICON_ARRAY'][$type];
	$render['msg']		= $message;
	$render['buttons']	= $bts;
	$render['target'] 	= $target;
	$render['time'] 	= $Msonds;
	$render['url'] 		= $url;
	$render['_TPL_'] 	= 'tip';
	$render['_NOFOOTER_'] = TRUE;
	G('RENDER', $render);
}

function jsShowTip($type='info',$message='',$buttons=array(),$url='index.php',$seconds=10,$target='self')
{
	$mIcon=$GLOBALS['_TIMER_ICON_ARRAY'][$type];

	$_action=array('BACK'=>'history.go(-1)','CLOSE'=>'window.close();');

	$tmp="
	<div class=\"sysTip\">
	<span class=\"icon\">
	<img src=\"$mIcon\" width=\"57\" height=\"54\" />";
	if($seconds>0)
		$tmp.="	<label id=\"timer\" style=\"color:#f00;font-weight:700;font-size:22px;\">{$seconds}</label>";
	$tmp.="	</span>
	<span class=\"message\">$message</span>
	<span class=\"tip_buttons\">";
	if(is_array($buttons) && count($buttons)>0)
	{
	foreach($buttons as $button)
	{
	$tmp.=$button;
	}
	}
	$tmp.="</span>
	</div>";

	if($seconds>0)
	{
	$tmp.="<script type=\"text/javascript\">
	var timer_goto;
	function wait_goto()
	{
	var s_s,o_o;
	o_o=$('timer');
	s_s = isIE ? o_o.innerText : o_o.textContent;
	s_s=s_s-1;
	if(s_s>0)
	isIE ? o_o.innerText=s_s : o_o.textContent = s_s;
	else";
	if(key_exists($url,$_action))
	$tmp.="\n{$_action[$url]};";
	else
	$tmp.="
	{
	$target.location.href='$url';
	clearInterval(window.timer_goto);
	}";

	$tmp.="
	}
	window.timer_goto=setInterval(\"wait_goto()\",1000);
			</script>";
	}
			return $tmp;
	}

			/**
			+-----------------------------------------------------------
	* JS页面跳转
	+-----------------------------------------------------------
	* @param   string  $url    地址
	* @param   string  $alert  提示信息
	* @param   stting  $type   跳转类型
	* @param   string  $frame  目标窗口
	+-----------------------------------------------------------
	* @return  string
	+-----------------------------------------------------------
	*/
	function jsGoto($url,$alert='',$type='goto',$frame='window')
	{
	$tmp = '<script type="text/JavaScript">';
	if($alert!='')
	$tmp.="alert('$alert');";
	if ($type=="back" )
			$tmp .= "history.back(1);";
			elseif($type == "goto" )
			$tmp.=$frame.".location.href='$url';";
			elseif($type == "close")
			$tmp.=$frame.".close();";

			$tmp.="</script>";
			echo $tmp;
			return false;
	}

			/**
			+-----------------------------------------------------------
			* 构建JS函数
			+-----------------------------------------------------------
			* @param   string  $name_param JS内容
			+-----------------------------------------------------------
			* @return  string
			+-----------------------------------------------------------
			*/
			function jsFunction($name_param)
			{
			$result='<script type="text/javascript">'."\n";
			$result.=$name_param;
			$result.="\n</script>\n";
			return $result;
			}

			/**
			+-----------------------------------------------------------
			* 防注入处理(为变量加入斜杠)函数
			+-----------------------------------------------------------
			* @param   array   $array  要处理的数组
			+-----------------------------------------------------------
			* @return  array
			+-----------------------------------------------------------
			*/
			function add_s(&$array)
			{
				foreach($array as $key=>$value)
				{
				if(!is_array($value))
				{
				$array[$key]=addslashes($value);
				}
				else
					add_s($array[$key]);
				}
				}

				/**
				+-----------------------------------------------------------
				* 安全代码转换
				+-----------------------------------------------------------
				* @param   string  $d  欲转换的代码
				+-----------------------------------------------------------
				* @return  string
				+-----------------------------------------------------------
				*/
				function Safe_Convert($d)
				{
					$d = str_replace("<","&lt;",$d);
							$d = str_replace(">","&gt;",$d);
					$d = str_replace("  "," &nbsp;",$d);
					$d = str_replace("\"", "^yinhao^", $d);
					return $d;
					}

					/**
					+-----------------------------------------------------------
					* 深度转义
					* 如果是数组，遍历数组，将其中的特殊字符转义
					+-----------------------------------------------------------
					* @param   mixed   $value  要处理的内容
					+-----------------------------------------------------------
					* @return  mixed
					+-----------------------------------------------------------
					*/
					function addslashesDeep($value)
					{
					$value=is_array($value) ?
			  array_map('addslashes',$value) :
			  addslashes($value);
			  return $value;
			  }

			  /**
			  +-----------------------------------------------------------
			  * 替换部份特殊字符用于SQL写入
			  +-----------------------------------------------------------
			  * @param	string    $content  要处理的内容
			  * @param   int       $type     处理方式，0替换，1换回
			  	+-----------------------------------------------------------
			  	* @return	string
			  	+-----------------------------------------------------------
			  */
			  function specialStrchop($content,$type=0)
			  {
			  if($type=="0")
			  {
			  $content=str_replace("\"", "^yinhao^", $content);
			  $content=str_replace("'", "^danyinhao^", $content);
			  $content=ereg_replace("\n", "", $content);
			  $content=ereg_replace("\r", "{line}", $content);
			  }
			  else
			  {
			  $content=str_replace("^yinhao^", "\"",  $content);
			  $content=str_replace("^danyinhao^", "'",  $content);
			  $content=ereg_replace("{line}", "\n\r", $content);
			  }
			  return $content;
			  }

			  /**
			  +-----------------------------------------------------------
			  * 生成随机字符串（数字+大小写字母）
			  +-----------------------------------------------------------
			  * @param	int   $length     长度
			  * @param   int   $numeric    全为数字
			  +-----------------------------------------------------------
			  * @return
			  +-----------------------------------------------------------
			  	*/
			  	function random($length, $numeric = 0)
							{
							PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
									if($numeric==='0x')
									{
										$hash = '';
										$chars = 'ABCDEF0123456789';
										$max = strlen($chars) - 1;
										for($i = 0; $i < $length; $i++)
										$hash .= $chars[mt_rand(0, $max)];
			  }
										elseif($numeric)
										{
										$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
			  }
			  	else
			  	{
			  	$hash = '';
			  	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
			  	$max = strlen($chars) - 1;
			  	for($i = 0; $i < $length; $i++)
			  	{
			  	$hash .= $chars[mt_rand(0, $max)];
			  	}
			  }
			  return $hash;
			  }

			  // 参数解释
			  // $string： 明文 或 密文
			  // $operation：DECODE表示解密,其它表示加密
			  // $key： 密匙
			  // $expiry：密文有效期
			  function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
			  {
			  // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
			  $ckey_length = 4;

			  // 密匙
			  $key = md5($key ? $key : CONF('DEFAULT_KEY'));

			  // 密匙a会参与加解密
			  $keya = md5(substr($key, 0, 16));
			  // 密匙b会用来做数据完整性验证
			  $keyb = md5(substr($key, 16, 16));
			  // 密匙c用于变化生成的密文
			  $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
			  // 参与运算的密匙
			  $cryptkey = $keya.md5($keya.$keyc);
			  $key_length = strlen($cryptkey);
			  // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
			  // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
			  $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
			  $string_length = strlen($string);
			  $result = '';
			  $box = range(0, 255);
			  $rndkey = array();
			  // 产生密匙簿
			  for($i = 0; $i <= 255; $i++)
			  {
			  $rndkey[$i] = ord($cryptkey[$i % $key_length]);
			  }
			  // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
			  for($j = $i = 0; $i < 256; $i++)
			  {
			  $j = ($j + $box[$i] + $rndkey[$i]) % 256;
			  $tmp = $box[$i];
			  $box[$i] = $box[$j];
			  	$box[$j] = $tmp;
			  }
			  	// 核心加解密部分
			  	for($a = $j = $i = 0; $i < $string_length; $i++)
			  {
			  	$a = ($a + 1) % 256;
			  	$j = ($j + $box[$a]) % 256;
			  	$tmp = $box[$a];
			  	$box[$a] = $box[$j];
			  	$box[$j] = $tmp;
			  	// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE')
	{
		// 验证数据有效性，请看未加密明文的格式
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16))
			return substr($result, 26);
		else
			return '';
	}
	else
	{
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
function StringEncrypt($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	return authcode($string, $operation, $key, $expiry);
}

/**
 * @Function    截取语句的一部分
 * @param       $sentence 需要截取的字符串，$count 可显示的长度
 * @return      截取后的字符串
 */
function CUT_SENTENCE($sentence,$count=200)
{
	if(strlen($sentence)>$count)
		return array(UTF8_SUBSTR($sentence,0,$count).'. . .');
	else
		return $sentence;
}


/**
 * @Function utf-8编码取子字符串
 * @param    $str：目标字符串 $start：起始位置 $end：结束位置
 * @return   目标子符串
 */
function UTF8_SUBSTR($str,$start,$end)
{
	preg_match_all("/./u", $str, $ar);
	return join("",array_slice($ar[0],$start,$end));
}


/**
 * @Function 转义HTML代码
 * @param    $type='key':只转键名 $type='value':只转值 $type='both':全转
 * @return   转义后的HTML代码
 */
function HtmlentitiesDeep($value,$type='both')
{
	if(is_array($value))
	{
		foreach($value as $k=>$v)
		{
			if(is_array($v))
			{
				$v=HtmlentitiesDeep($v);
			}
			else
			{
				if($type=='both')
				{
					unset($value[$k]);
					$k		  = htmlentities($k,ENT_QUOTES,'UTF-8');
					$value[$k]= htmlentities($v,ENT_QUOTES,'UTF-8');
				}
				elseif($type=='key')
				{
					unset($value[$k]);
					$k		  = htmlentities($k,ENT_QUOTES,'UTF-8');
					$value[$k]= $v;
				}
				else
				{
					$value[$k]= htmlentities($v,ENT_QUOTES,'UTF-8');
				}
			}
		}
	}
	else
	{
		$value=htmlentities($value,ENT_QUOTES,'UTF-8');
	}
	return $value;
}

/**
 * 自动设置待取消禁用状态的表单
 +-----------------------------------------------------------
 * 为适应 jQuery 应用而为id自动添加 # 符号
 +-----------------------------------------------------------
 * 后续过程在 /WebCore/core/view.class.php 里处理
 *
 */
function ADD_TAG_FORM_DISABLED($id)
{
	$id && $GLOBALS['__FORM_DISABLED'][]='#'.$id;
}


/**
 +-----------------------------------------------------------
 * 跳转到其他页面
 +-----------------------------------------------------------
 * @param	string	$url	地址
 +-----------------------------------------------------------
 * @return	void
 +-----------------------------------------------------------
 */
function Redirect($url, $frame='')
{
	if($frame=='')
		Header('Location: '.$url);
	else
	{
		header('Content-Type: text/html;charset=utf-8');
		echo "<script type=\"text/javascript\">{$frame}.location.href='{$url}';</script>";
	}
	die;
}

function GEN_AUTO_ID($fix='arid_')
{
	return $fix.Random(3);
}


/**
 * 将两级序列字符串按给定的键值导出到一个数组里
 * 是 SERIAL_ENCODE 方法的逆向过程
 *
 * @example:SERIAL_DECODE($wgc_columns_define,'key,doo_tpl_list,doo_tpl_edit,...','wgci_')
 * @return array
 */
function SERIAL_DECODE($serial,$dump_to_define,$prefix='')
{
	$_=explode(SYS_SERIAL_GLUE_Y,$serial);
	$__=explode(GLUE,$dump_to_define);
	$result=array();
	foreach($__ as $i=>$v)
	{
		$result[$prefix.$v]=explode(SYS_SERIAL_GLUE_X,$_[$i]);
	}
	return $result;
}

/**
 * @abstract:将数组里给定的键值的序列字符串分割为数组,引用传递参数
 * @example:SERIAL_2_ARRAY($wgc_data,'auto_items,overwrite,...','wgc_');
 * @return void
 */
function SERIAL_2_ARRAY(&$data,$items,$prefix='')
{
	$_=explode(GLUE,$items);
	foreach($_ as $k)
	{
		$data[$prefix.$k]=$data[$prefix.$k]
		? explode(GLUE,$data[$prefix.$k])
		: '';
	}
	return true;
}


function ENTER($tab_count=0,$enter_count=1,$force=false)
{
	return !$force && CONF('html_output_zip')
	? ''
	: str_repeat(chr(10),$enter_count).str_repeat(chr(9),$tab_count);
}
function FENTER($tab_count=0,$enter_count=1)
{
	return str_repeat(chr(10),$enter_count).str_repeat(chr(9),$tab_count);
}

//数据JSON消息
function json_echo($result, $msg)
{
	@ob_clean();
	echo json_encode(array(
			'r'=>$result,
			'm'=>$msg
	));
	die;
}

/**
 * 自动存储要注册的事件和方法
 +-----------------------------------------------------------
 * 由 base.js 里调用 jQuery.bind 方法
 +-----------------------------------------------------------
 * 后续过程在 /WebCore/core/view.class.php 里处理
 */
function ADD_TAG_SCRIPTS($id,$action,$event)
{
	$id && $GLOBALS['_TAG_SCRIPTS'][]=array($id,$action,$event);
}


function span($str, $style='red')
{
	return '<span'.($style?' class="'.$style.'"':'').'>'.$str.'</span>';
}

/**
 +-----------------------------------------------------------
 * 去掉字符串重复值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function StringUnique($string,$compart)
{
	$array		 = explode($compart,$string);
	$unique		 = array_unique($array);
	$string		 = implode($compart,$unique);
	return $string;
}

/**
 +-----------------------------------------------------------
 *安全规则管理行为限定值文本框字符串排序
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function getStringSort($string,$compart=",")
{
	$array= explode($compart,$string);
	asort($array);
	$string= implode($compart,$array);
	return $string;
}

/** 根据传入文本是否为空确定是否使用模板格式化处理
 * 
 * @example 1	:$tpl:string, $value:string		-->	如果 $value 文本长度不为0,则使用模板格式化之
 * 
 * @example	2	:$tpl:string, $value:array		-->	sprintf 函数的另一种写法,将待替换变量以数组形式传入
 * 
 * @example	3	:$tpl:array, $value:array		-->	依次匹配 $tpl 和 $value的每一个元素,循环(递归)做示例1的所有处理
 */
function STRING_FORMAT($tpl, $value, $default='')
{
	$result='';
	if(is_string($tpl) && CHECK_ARRAY($value))
	{
		array_unshift($value, $tpl);
		return call_user_func_array('sprintf', $value);
	}
	elseif(CHECK_ARRAY($tpl) && CHECK_ARRAY($value))
	{
		$n_tpl=count($tpl);
		$n_val=count($value);

		if($n_tpl==$n_val && $n_val>1)
			foreach($tpl as $i=>$_tpl)
				$result.=STRING_FORMAT($_tpl, $value[$i], $default);
		else
			foreach($value as $val)
				$result.=STRING_FORMAT($tpl[0], $val, $default);
	}
	elseif(strlen($value))	// 仅当文本串不为空时才执行格式化
	{
		$result.=sprintf($tpl, $value);
	}

	$result || $result=$default;
	return $result;
}
/******************以下存放手机处理的函数*************************/
/**
 * @todo:   格式化手机号码为固定格式
 * @param:  $num    => 手机号码
 * @param:  $none   => 无号码时返回的固定格式
 * @return: 手机号码格式化后的字符串
 * */
function PHONE_NUMBER_FORMAT($num, $none='未知号码')
{
	return $num
		? substr($num,0,3).' '.substr($num,3,4).' '.substr($num,7)
		: $none;
}
function CheckUnameEn($str,$min=5,$max=10)
{
    $pattern='/^[a-zA-Z_][0-9a-zA-Z_]{'.$min.','.$max.'}$/';
    return preg_match($pattern,$str);
}
function CheckUnameCh($str,$min=5,$max=10)
{
     $pattern="/^[a-z0-9_\x{4e00}-\x{9fa5}]{".$min.",".$max."}$/u";
     return preg_match($pattern,$str);
}