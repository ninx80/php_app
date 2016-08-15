<?php
/**
 +-----------------------------------------------------------
 * PHP数组转JS串
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function phpArr2jsSerial($pArr,$item='keys',$center_fix=',',$pre_nail_fix=',')
{
	if($item=='keys')
		$result=implode($center_fix,array_keys($pArr));
	else
		$result=implode($center_fix,array_values($pArr));
	$result=$pre_nail_fix.$result.$pre_nail_fix;
	return $result;
}

/**
 +-----------------------------------------------------------
 * PHP数组转JS数组
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return	string
 +-----------------------------------------------------------
 */
function phpArr2jsArr($pArr)
{
	if(is_array($pArr))
	{
		$str=implode('\',\'',$pArr);
		$str="'$str'";
		return $str;
	}
	else
	{
		return "'--NOT-ARRAY--'";
	}
}

/**
 +-----------------------------------------------------------
 * PHP数组过虑 返回$full_array中键名或值在$key_array的一个数组
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return	array
 +-----------------------------------------------------------
 */
function array_x_filter($full_array,$key_array,$with_flip=false)
{
	$result=array();
	if(!is_array($full_array) || !is_array($key_array))
		$result[0]='--NOT-ARRAY--';
	else
	{
		foreach($full_array as $k=>$v)
		{
			if(in_array($k,$key_array))
				$result[$k]=$v;
			elseif(in_array($v,$key_array))
			$result[$k]=$v;
		}
	}
	if($with_flip)
		$result=array_flip($result);
	return $result;
}

/**
 * 合并2维数组
 * @param array $arr 2维数组
 * @return array
 */
function array_unique_2D($arr)
{
	$tmpArr= $arr;
	$len = count($arr);
	for($i=0;$i<$len;$i++)
	{
		$j=$i+1;
		for($j;$j<$len;$j++)
		{
			if(count(array_intersect_assoc($arr[$i], $arr[$j])) == count($arr[$i]))
			{
				unset($tmpArr[$i]);
			}
		}
	}


	return $tmpArr;
}

/**
 +-----------------------------------------------------------
 * PHP自定限得数组中的元素
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return
 +-----------------------------------------------------------
 */
function array_x_return($in_array,$getType='key-value')
{
	switch($getType)
	{
		case 'array':
			$result=$in_array;
			break;
		case 'array-keys':
			$result=array_keys($in_array);
			break;
		case 'array-values':
			$result=array_values($in_array);
			break;
		case 'string-key':
			global $waasai;
			$result=$waasai->WSON_encode($in_array,'keys');
			break;
		case 'string-value':
			global $waasai;
			$result=$waasai->WSON_encode($in_array,'values');
			break;
	}

	return $result;
}

/**
 * @mName 	 SORT_FOR_PLANAR_ARRAY()
 * @Purpose   以二维数组中的某个元素进行大小比较,对二维数组进行排序.
 * @Parameter array $array 二维数组
 * 			 int   $element 元素
 * 			 string $sort  降序或升序,默认降序
 * @Return    返回排序后的数组
 */
function SORT_2LEVEL_ARRAY($array,$sort='DESC',$element='')
{
	if(!is_array($array))
		return $array;

	//判断维数,对数组进行验证，判断每项是否数组，若是是否包含指定元素
	$tmp_arr=$arr=$new_array=array();

	for($i=0,$len=count($array);$i<$len;$i++)
	{
		if(!is_array($array[$i]))	//将非数组项放进$tmp_arr
			$tmp_arr[$i]=$array[$i];
		elseif(!array_key_exists($element,$array[$i]))			//将无$element元素的数组项放进$arr
		$arr[$i]=$array[$i];
		elseif(preg_match("/[^0-9]+/i",$array[$i][$element]))	//若元素中含有非数字，就不能排序比较，则放进$arr，
		$arr[$i]=$array[$i];
		else
			$new_array[$i]=$array[$i];
	}

	//对$array满足所有要求的项进行排序
	$count=count($new_array);
	for($i=0;$i<$count;$i++)
	{
		for($j=$count-1;$j>=$i;$j--)
		{
			if(($sort=='DESC' && $new_array[$i][$element]<$new_array[$j][$element]) || ($sort=='ASC' && $new_array[$i][$element]>$new_array[$j][$element]))
			{
				$tmp=$new_array[$i];
				$new_array[$i]=$new_array[$j];
				$new_array[$j]=$tmp;
			}
		}
	}
	$count_tmp_arr=count($tmp_arr);
	if($count_tmp_arr>0)
	{
		if($sort=='DESC')
			arsort($tmp_arr);
		elseif($sort=='ASC')
		asort($tmp_arr);
	}
	$tmp_arr=$count_tmp_arr>0
	? $tmp_arr
	: array();
	$arr=count($arr)>0
	? $arr
	: array();
	$new_array=array_merge($new_array,$tmp_arr,$arr);

	return $new_array;
}

/**
 +-----------------------------------------------------------
 *通过键名取得指定数组的值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return string
 +-----------------------------------------------------------
 */
function GET_QUERY_ARRAY_VALUE($array,$value,$none_string='')
{
	if(!isset($array,$value,$array[$value]))	return $none_string;
	return $array[$value];
}

function REPLACE_ARRAY(&$string,&$array)
{
	$string=str_replace(array_keys($array),array_values($array),$string);
	return $string;
}
function GET_REPLACE_ARRAY($string,$array)
{
	$string=str_replace(array_keys($array),array_values($array),$string);
	return $string;
}

function pA2jA($array)
{
	if(!$array)
		return '\'\'';
	$array = (array)($array);
	return '[\''.implode('\',\'', $array).'\']';
}



/**
 +-----------------------------------------------------------
 * 将一维数组转换为字符串
 +-----------------------------------------------------------
 * @param	array	$array		要转换的数组
 * @param	string	$item_spe	每个元素间的分隔符
 * @param	string	$kv_spe		键和值间的分隔符，如果为null，则不显示键名
 +-----------------------------------------------------------
 * @return	mixed	返回true 或不在返回中的IP段
 +-----------------------------------------------------------
 */
function array2serial($array, $item_spe=' ', $kv_spe=null)
{
	$tmp = array();
	foreach($array as $k=>$v)
		if(!is_null($kv_spe))
		$tmp[] = $k.$kv_spe.$v;
	else
		$tmp[] = $v;
	return implode($item_spe, $tmp);
}


/**
 * @name IS_2D_ARR
 * @todo  判断数组是否为2维数组
 * @param  array      $arr
 * @return boolean
 * @example 若非以下类型的数组则返回false;否则为true
 *   $arr=array(
 *       0=>array(),
 *       1=>array(),
 *       2=>array()
 *   )
 */
function IS_2D_ARR($arr)
{
	if(!is_array($arr) || !count($arr))
	{
		return false;
	}
	foreach($arr as $key=>$value)
	{
		if(!is_numeric($key) && !is_array($value))
		{
			return false;
		}
	}

	return true;
}

/**
 +-----------------------------------------------------------
 * 搜索数组
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return	array
 +-----------------------------------------------------------
 */;
function getSearchValue($array)
{
	if(is_array($array))
	{
		foreach($array as $key=>$value)
		{
			if(!is_array($value))
			{
				$array[$key]=htmlentities($value, ENT_QUOTES,'UTF-8');
			}
			else
				getSearchValue($array[$key]);
		}
	}
	return $array;
}

/**
 +-----------------------------------------------------------
 *通过键名取得指定数组的值
 * 如果键名也是数组 ,则以该数组做为键名,母数组的值返回
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return string
 +-----------------------------------------------------------
 */
function GET_ARRAY_VALUE($array,$key,$none_string=null)
{
	is_null($none_string) && $none_string='';

	if(isset($array,$key))
		if( is_array($key) )
		return implode(GLUE,array_x_filter($array,$key));
	elseif(isset($array[$key]))
	return $array[$key];

	return $none_string;
}
/**
 * 将传入的数组(数组,数组,数组...)类型的数据编码为两级分割符的文本串
 *
 * @param array		: 一般为 DigPost 方法获取的数据
 * @return string	: 格式化的带两级分割符的文本串
 * @example	:
 * $x=array('wgci_name'=>array('a','b','c'),'wgci_key'=>array(1,2,3));
 * echo SERIAL_ENCODE($x);
 *
 * // a|b|c##1|2|3
 */
function SERIAL_ENCODE($array)
{
	$_=array();
	foreach($array as $v)
	{
		$_[]=implode(SYS_SERIAL_GLUE_X,$v);
	}
	return implode(SYS_SERIAL_GLUE_Y,$_);
}


function ARRAY_2_SERIAL(&$data,$items,$prefix='')
{
	$_=explode(GLUE,$items);
	foreach($_ as $k)
	{
		$data[$prefix.$k]=isset($data[$prefix.$k]) && $data[$prefix.$k]
		? implode(GLUE,$data[$prefix.$k])
		: '';
	}
	return true;
}


function digArray($arr,$filter,$trim=false)
{
	$result = array();
	foreach ($arr as $k=>$value)
	{
		if(strpos($k, $filter)===0)
			$result[($trim ? substr($k, strlen($filter)) : $k)] = $value;
	}

	return $result;
}
/**
 +-----------------------------------------------------------
 * 出栈数组中的指定值
 +-----------------------------------------------------------
 * @param
 +-----------------------------------------------------------
 * @return void
 +-----------------------------------------------------------
 */
function POP_ARRAY(&$array,$key,$none='')
{	if($array && !is_array($array))
		$array = (array)$array;
		
	if(isset($array[$key]))
	{
		$tmp=$array[$key];
		unset($array[$key]);
	}
	else
	{
		$tmp=$none;
	}

	return $tmp;
}
function array_digg($set, &$arrTmp=array())
{
	static $result=array();

	foreach($set as $s)
		if(is_array($s))
			array_digg($s);
		else
			in_array($s, $result) || $result[]=$s;

	return $result;
}