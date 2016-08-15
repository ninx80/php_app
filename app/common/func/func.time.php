<?php
/**
 +-----------------------------------------------------------
 * 获取格式化的当前时间
 +-----------------------------------------------------------
 * @param   string  $format 格式化方式
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function now($format="Y-m-d H:i:s")
{
	return FormatTime(0, '', false);
}


/**
 +-----------------------------------------------------------
 * 获取格式化后的时间
 +-----------------------------------------------------------
 * @param	int     $t      时间戳
 * @param   string  $format 格式化方式
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function mFormatTime($t=0,$format="Y-m-d H:i:s")
{
	return FormatTime($t, $format, false);
}


//格式化时间
function FormatTime($t=0,$format='Y-m-d H:i:s',$hideYear=true,$addPretty=false)
{
	if($t===false)
		return '--';

	(defined('NOW') && NOW>0) || define('NOW', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time());
	$format || $format='Y-m-d H:i:s';
	$_time=NOW;
	$t==0 && $t=$_time;

	($hideYear==true) && (date('Y',$_time) == date('Y',$t)) && ($format=str_replace('Y-','',$format));

	return date($format,$t).($addPretty?'<span>'.GET_PRETTY_DATE($t).'</span>':'');
}

function FormatDate($t=0,$format='Y-m-d',$hideYear=true)
{
	$format || $format='Y-m-d';
	return FormatTime($t,$format,$hideYear);
}

function TO_TIME($t,$p=false)
{
	return FormatTime($t,'',true,$p);
}
function TO_DAYS($date)
{
	return $date
		? ceil(strtotime($date) /86400) + 719528
		: '';
}
function FROM_DAYS($d,$warn_cord=false)
{
	if($d!==0 && !$d)	return '';

	$w=($warn_cord+$d+713070) *86400;
	$d=($d+713070) *86400;
	
	return FormatDate( $d ,'',false);
}
function TO_WEEKDAY($date)
{
	return date('w',is_numeric($date) ? ($date+722070) *86400 : strtotime($date) );
}
function FROM_WEEKDAY($date)
{
	return $GLOBALS['_WEEKDAY'][$date];
}


/**
 +-----------------------------------------------------------
 * 获取美化日期间隔
 +-----------------------------------------------------------
 * @param	int   $t      时间
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function GET_PRETTY_DATE($t=0)
{
	defined('_NOW_') || define('_NOW_',$_SERVER['REQUEST_TIME']);
	$t || $t=_NOW_;

	$diff_sec=_NOW_-$t;
	$diff_day=floor($diff_sec / 86400);

	$diff_year=date('Y',_NOW_)-date('Y',$t);	//1900-2100
	$diff_month=date('n',_NOW_)-date('n',$t);	//1-12
	$diff_date=date('z',_NOW_)-date('z',$t);	//0-366
	$diff_week=date('W',_NOW_)-date('W',$t);	//0-6
	$diff_part=(date('A',_NOW_) == date('A',$t));	//AM / PM
	$monthofyear=date('n',$t);

	if((!$diff_day && $diff_day!=0) || $diff_day<0)	// 只能计算之前的时间
		return '';

	$hour=date('G',$t);	// 0-23
	$pofday=$hour<23
	? $hour<19
	? $hour<18
	? $hour<14
	? $hour<12
	? $hour<8
	? $hour<5
	? $hour<3
	? '午夜'
	: '凌晨'
	: '清晨'
	: '上午'
	: '中午'
	: '下午'
	: '傍晚'
	: '晚上'
	: '午夜';

	if($diff_date==0)	// 今天
	{
		if(!$diff_part)			return '今天'.$pofday;
		if($diff_sec > 7200)	return floor($diff_sec / 3600).'小时前';
		if($diff_sec > 3600)	return '1小时前';
		if($diff_sec > 1800)	return '半小时前';
		if($diff_sec > 120)		return floor($diff_sec / 60).'分钟前';
		if($diff_sec > 60)		return '1分钟前';
		return '刚才';
	}
	else
	{
		$mofday=CONF('DAYPART');
		$mofday=$mofday[date('A',$t)];
		$weekday=CONF('WEEKDAY');
		$weekday='星期'.$weekday[date('w',$t)];

		if($diff_year > 1)		return date('Y',$t).'年'.$monthofyear.'月';
		if($diff_year == 1)		return '去年'.$monthofyear.'月';
		if($diff_month > 1)		return $diff_month.'个月前';
		if($diff_month == 1)	return '上个月';
		if($diff_week > 1)		return ceil($diff_date / 7).'周前,'.$weekday.$pofday;
		if($diff_week == 1)		return '上周,'.$weekday.$pofday;
		if($diff_date > 2)		return $diff_date.'天前,'.$weekday.$pofday;
		if($diff_date == 2)		return '前天'.$pofday;

		return '昨天'.$pofday;
	}
}

/**
 +-----------------------------------------------------------
 *比较日期大小而获得开始时间和结束时间的值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function DateSearchCompare(&$date_from,&$date_to)
{
	$start = strtotime($date_from);
	$end   = strtotime($date_to);
	if(($start > $end) && $date_to!='')
	{
		$date_from	=mFormatTime($end,'Y-m-d');
		$date_to	=mFormatTime($start,'Y-m-d');
	}
}

/**
 * 根据自定义格式字符串计算有效期时间
 * 
 * @param	$timeout	string	自定义格式的字符串：0 1s 2n 3h 4d 5m 6t 7y
 * @param	$type		integer	1=新添加,2=修改	
 * 
 * @return	integer		当前时间戳NOW + 计算值
 */
if(!function_exists('__GetSessionExpireTime'))
{
	function __GetSessionExpireTime($timeout,$type=1)
	{
		if($timeout===0 || $timeout==='')
			return '0';
	
		$tt=substr($timeout,-1);
		$t=substr($timeout,0,-1);
		switch($tt)
		{
			case 's':return NOW + $t;
			case 'n':return NOW + $t*60;
			case 'q':return NOW + $t*900;
			case 'h':return NOW + $t*3600;
			case 'd':return NOW + $t*86400;
			case 'w':return NOW + $t*604800;
			case 'm':return NOW + $t*18144000;
			case 'y':return NOW + $t*217728000;
		}
	}
}