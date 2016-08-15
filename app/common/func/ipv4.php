<?php
/*IPv4的操作函数*/


/**
 +-----------------------------------------------------------
 * 获取远程客户端IP地址
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
if(!function_exists('get_ip'))
{
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
	
}

/**
 +-----------------------------------------------------------
 * 检测是否符合IP格式
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return 
 +-----------------------------------------------------------
 */
if(!function_exists('isIP'))
{
	function isIP($str)
	{
		if( preg_match('/^([0-9]{1,3}\.){1,3}[0-9]{1,3}$/', $str , $result))
		{
			$part=explode('.',$result[0]);
			if( count($part)==4 && $part[0] < 256 && $part[1] < 256 && $part[2] < 256 && $part[3] < 256)
				return true;
			else
				return false;
		}
		else
			return false;
	}
}
/**
 +-----------------------------------------------------------
 * 格式化IP地址格式 去掉多余的0
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return 
 +-----------------------------------------------------------
 */
function ParseIp($ip)
{
	$ip_arr=explode('.',$ip);
	if(isIP($ip) || count($ip_arr)==4)
	{
		
		$new_arr=array_map('intval',$ip_arr);
		$ip=implode('.',$new_arr);
	}
	return $ip;
}

/**
 +-----------------------------------------------------------
 * 将IP地址补充为等长，每个字节占三个字符
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return 
 +-----------------------------------------------------------
 */
function GET_PRETTY_IP($ip)
{
	/*$_ip=explode('.',$ip);
	$result=$_ip[0];
	for($i=1;$i<=3;$i++)
	{
		$__ip=$_ip[$i];
		$result.='.'.($__ip<100?($__ip<10?'&nbsp;&nbsp;'.$__ip:'&nbsp;'.$__ip):$__ip);
	}
	return $result;
	*/
	$parts = explode('.', $ip);
	foreach($parts as &$part)
		$part = ($part>99 ? '' : ($part>9 ? '&nbsp;' : '&nbsp;&nbsp;')).$part;

	return implode('.', $parts);
}

/**
 +-----------------------------------------------------------
 *比较IP大小
 +-----------------------------------------------------------
 * @param	
 +-----------------------------------------------------------
 * @return 
 +-----------------------------------------------------------
 */
function IpCompare(&$begin,&$end)
{
	$obegin = $begin;
	$oend = $end;
	
	if(!is_long($begin))
		$begin = IP2INT($begin);
	if(!is_long($end))
		$end = IP2INT($end);
		
	if($begin > $end)
	{
		$begin	= $oend;
		$end	= $obegin;
	}
	else
	{
		$begin	= $obegin;
		$end	= $oend;
	}
}

/**
+-----------------------------------------------------------
* 检查并整理重叠的IP段
+-----------------------------------------------------------
* @param	array	$iprange	需要检查的IP段
* @param	int		$outformat	传出的数据格式 0为long，1为IP
+-----------------------------------------------------------
* @return	array
+-----------------------------------------------------------
*/
function iprange_fold($iprange, $outformat=0)
{
	//转换为数字键名的数组
	$tmp = array();
	foreach($iprange as $k=>$v)
		$tmp[] = array( preg_match('/^\d+$/', $k) ? $k : IP2INT($k)=> preg_match('/^\d+$/', $v) ? $v : IP2INT($v));
	$iprange = $tmp;
	$len = count($iprange);

	for($i=0; $i<$len; $i++)
	{
		if(!key_exists($i, $iprange))
			continue;
		
		//游标，遍历每一个目标，如果与目标存在交集，合并目标并将目标删除
		//若到尾部，则换下一个元素作游标，重复循环，直到最后一个元素
		$cursor['s'] = (string)key($iprange[$i]);
		$cursor['e'] = $iprange[$i][$cursor['s']];
		for($j=0; $j<$len; $j++)
		{
			if($i==$j || !key_exists($j, $iprange))
				continue;

			//目标
			$target['s'] = (string)key($iprange[$j]);
			$target['e'] = $iprange[$j][$target['s']];

			//如果存在交集
			if( !( ($cursor['s']-1)>$target['e'] || ($cursor['e']+1)<$target['s']) )
			{
				//将目标合并到游标上
				if($cursor['s'] > $target['s'])
					$cursor['s'] = $target['s'];
				if($cursor['e'] < $target['e'])
					$cursor['e'] = $target['e'];
				$iprange[$i] = array( $cursor['s']=>$cursor['e'] );
				//删除目标
				unset($iprange[$j]);
			}
		}
	}
	//转换回IP段格式
	$tmp = array();
	foreach($iprange as $row)
	{
		$ips = key($row);
		$ipe = array_shift($row);
		if($outformat)
		{
			$ips = long2ip($ips);
			$ipe = long2ip($ipe);
		}
		$tmp[$ips] = $ipe;
	}
	$iprange = $tmp;
	return $iprange;
}

/**
+-----------------------------------------------------------
* 计算IP段的差集
* IP段 起 -- 始 表示为 key => value
+-----------------------------------------------------------
* @param	array	$range		IP段
* @param	array	$subtract	要减去的IP段
* @param	int		$outformat	传出的数据格式 0为long，1为ip
+-----------------------------------------------------------
* @return	array
+-----------------------------------------------------------
*/
function iprange_diff($range, $subtract, $outformat=1)
{
	foreach($subtract as $sips=>$sipe)
	{
		$sips = IP2INT($sips);
		$sipe = IP2INT($sipe);
		
		$result = array();
		foreach($range as $rips=>$ripe)
		{
			$rips = IP2INT($rips);
			$ripe = IP2INT($ripe);

            //修正ip范围差值计算中出现的bug。 zmb 2012.10.09
			if( $rips>$sipe || $sips>$ripe)	//没有交集的情况
			{
				$result[long2ip($rips)] = long2ip($ripe);
			}
			else							//有交集的情况
			{	
				//前半段
				if($sips > $rips)
                {
					 $result[long2ip($rips)] = long2ip($sips-1);
                }
                     
				//后半段
			 	if($sipe < $ripe) 
                { 
					 $result[long2ip($sipe+1)] = long2ip($ripe);
                }
			 }
            
		}
		$range = $result; 
		if(!$range)
			break;
	}
    
	//结果是否需要格式化成IP格式
	if($outformat===0)
	{
		$tmp = array();
		foreach($range as $s=>$e)
            $tmp[IP2INT($s)] = IP2INT($e);            
		$range = $tmp;
	}
	return $range;
}

/**
+-----------------------------------------------------------
* 合并IP段
* IP段 起 -- 始 表示为 key => value
+-----------------------------------------------------------
* @param	array	$range1		IP段1
* @param	array	$range2		IP段2
* @param	int		$outformat	传出的数据格式 0为long，1为IP
+-----------------------------------------------------------
* @return	array
+-----------------------------------------------------------
*/
function iprange_merge($range1, $range2, $outformat=0)
{
	foreach($range2 as $ips=>$ipe)
	{
		//如果段2的主键不在段1中，或段1的值大于段2的值
		if(!key_exists($ips, $range1) || $ipe > $range1[$ips])
			$range1[$ips] = $ipe;
	}

	$range1 = iprange_fold($range1, $outformat);
	return $range1;
}

/**
+-----------------------------------------------------------
* 检查一个或多个IP段是否在IP范围中(IP范围也可为一个或多个IP段)
* IP段 起 -- 始 表示为 key => value
+-----------------------------------------------------------
* @param	array	$needle		要检查的IP段
* @param	array	$range		IP范围
* @param	int		$outformat	传出的数据格式 0为long，1为IP
+-----------------------------------------------------------
* @return	mixed	返回true 或不在返回中的IP段
+-----------------------------------------------------------
*/
function in_iprange($needle, $range, $outformat=0)
{
	$not_in = array();
	
	foreach($needle as $nips=>$nipe)
	{
		$nips = IP2INT($nips);
		$nipe = IP2INT($nipe);
		$in_range = false;

		foreach($range as $rips=>$ripe)
		{
			$rips = IP2INT($rips);
			$ripe = IP2INT($ripe);
			$in_part = false;

			if( $rips<=$nips && $ripe>=$nipe )	
				$in_part = true;
				
			$in_range = $in_range | $in_part;
		}
		
		if(!$in_range)
			$not_in[$nips] = $nipe;
	}
	if($outformat)
	{
		$tmp = array();
		foreach($not_in as $s=>$e)
			$tmp[long2ip($s)] = long2ip($e);
		$not_in = $tmp;
	}
	return count($not_in) ? $not_in : true;
}

function mGetMAC($mac)
{
    $mac = str_split($mac, 2);
    $mac = implode('-', $mac);
    return $mac;
}

function IP2INT($ip)
{
    //int的取值范围为-2147483647~2147483648。当超过范围时，int转换会返回2147483648。   by zmb 2013.01.28
	if(!isIP($ip) && is_numeric($ip))
		return !is_string($ip)?$ip:sprintf('%u', ($ip-4294967296));
	
    $t = sprintf('%u', ip2long($ip));
    if($t == 0 && $ip != '0.0.0.0')
        $t = 0;    
    return $t;
}

function INT2IP($ip)
{
    return long2ip($ip);
}

function MASK2STRING($mask)
{
    //需要检测是否是格式正确的MASK
    // 使用 IS_MASK 方法
    // 完成后删除该注释

    $str = decbin(0xffffffff << MASK2INT($mask));
    return $str;
}
function MASK2INT($mask)
{
    //需要检测是否是格式正确的MASK
    // 使用 IS_MASK 方法
    // 完成后删除该注释

    return strpos(decbin(ip2long($mask)), '0');
}
function INT2MASK($int)
{
	$int = sprintf('%u', 0xffffffff << (32-$int));
	return long2ip($int);
}
function IS_MASK($mask)
{
	$mask = ip2long($mask);
	$mask = decbin($mask);
	return strpos($mask,'01')===false;
}

//取得用户IP
function GET_REMOTE_IP()
{
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    elseif(isset($_SERVER['HTTP_CLIENT_IP']))
        $realip = $_SERVER['HTTP_CLIENT_IP'];
    else
        $realip = $_SERVER['REMOTE_ADDR'];
    return $realip;
}

//10进制转换2进制用于验证MASK
function decextbin($decimalnumber, $bit)
{
    $maxval = 1;
    $sumval = 1;
    for($i = 1; $i < $bit; $i++)
    {
        $maxval = $maxval * 2;
        $sumval = $sumval + $maxval;
    }
    if($sumval < $decimalnumber)
        return false;
    for($bitvalue = $maxval; $bitvalue >= 1; $bitvalue = $bitvalue / 2)
    {
        if(($decimalnumber / $bitvalue) >= 1)
            $thisbit = 1;
        else
            $thisbit = 0;
        if($thisbit == 1)
            $decimalnumber = $decimalnumber - $bitvalue;

        $binarynumber .= $thisbit;
    }
    return $binarynumber;
}

function ipRev($ipint)
{
    $ip_arr=explode('.',long2ip($ipint));
    $ip_arr=array_reverse($ip_arr);
    return implode('.',$ip_arr);
}
/** 转换nvr传出的负值IP为字符串IP*/
/** -1806692180 -->> '172.16.80.108' */
function omniInt2IP($ipint)
{
    $ip4 = abs($ipint >> 24); 
    $ip3 = ($ipint >> 16) & 255;
    $ip2 = ($ipint >> 8) & 255;
    $ip1 = $ipint & 255;
    return $ip1.'.'.$ip2.'.'.$ip3.'.'.$ip4;
};
/** 转换字符串IP为nvr传出的负值IP*/
/** '172.16.80.108' -->> -1806692180 */
function omniIP2Int($ipstr)
{
    $ips = explode('.', $ipstr);
    return ((-$ips[3] << 24)) + ($ips[2] << 16) + ($ips[1] << 8) + $ips[0];
}

?>