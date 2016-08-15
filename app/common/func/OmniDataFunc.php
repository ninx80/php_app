<?php

/**
 * 处理字符串
*/
 function str_dest($str, $center, $offset)
 {
	$res = '';
	$sdata = substr($str, $center, $offset);
	for($i=0; $i<$offset; $i++)
	{
		$s = substr($sdata, $i, 1);
		if(ord($s) !== 0)
			$res .= $s;
	}
	return $res;
 }

function int_long_dest($str, $center, $offset)
{	
	if($offset === 4)
	{
		$data = unpack('Ldata', substr($str, $center, $offset));
		if(is_array($data))
		{
			$newstr = '';
			foreach($data as $k => $v)
			{  
				$newstr .= $v;
			}
			return $newstr;
		}

	}
	else if($offset === 8)
	{
		$data = unpack('Ldata1/Ldata2', substr($str, $center, $offset));
		if(is_array($data))
		{
			$newstr = '';
            $floatNum = 0;
			foreach($data as $k => $v)   //高四位*(2的32次方)
			{  
				if($k === 'data2')
                {
                    $floatNum = intval($v, 10)*4294967296;
                }
			}
			foreach($data as $k => $v)   //低四位被加上
			{  
				if($k === 'data1')
                {
                    $floatNum += intval($v, 10);
                }
			}
            if($floatNum !== 0)
                $newstr = (string)$floatNum;
			return $newstr;
		}
	}
	else
		return NULL;
}

 /**
 * ********************** Session files and data functions ***************************
 */
 
 /**
 *  检查SessionID文件夹以及DID文件是否存在，最长检测 $outTime 毫秒
 *  SessionID文件存在，返回 countTime ；不存在，返回 0
 */
function checkSessionIDDir($SessionID, $DID)
{
    $dir = OMNI_DATA_ROOT.$SessionID;
        //echo "$dir<br>";
	$flag = 0;
    $countTime = 0;
    $top = ($GLOBALS['TIME_OUT'])/10;
	for($i=0; $i<$top; $i++)			         // 10ms每次
	{
	   if(is_dir($dir))
        {
            if(DEBUG_MODE) echo "dir $DID  countTime = $countTime ************* <br>";
            $flag = 1;
            break;
        }
        usleep(10000);
        $countTime += 10;
	}
	if($flag === 1)			//SessionID文件夹已经存在，继续检测DID文件是否存在
	{
        $fileFlg = 0;
        $num = ($GLOBALS['TIME_OUT']-$countTime)/10;
        for($i=0; $i<$num; $i++)
        {
            foreach(scandir($dir) as $k=>$v)
            {
                if( $DID == substr($v, 0, strpos($v, '_')) )
                {
                    if(DEBUG_MODE) echo "file $DID  countTime = $countTime ************* <br>";
                    $fileFlg = 1;
                    break;
                }
            }
            if($fileFlg === 1)
                break;
            else
            {
                usleep(10000);
                $countTime += 10;
            }
        }
        return $countTime;
	}
	else
	{
		return 0;		   //超时，不存在
	}
}


/**
 *  获取DID数据
 *  成功返回三维数组：array[DID][file name][ SessionID | DID | ret | data ]
 *  失败返回 0
 */
function getDIDFileData($SessionID, $DID)  /** 如果有多个文件的话，只取DID一致的最后一个文件的data*/
{
    $dir = OMNI_DATA_ROOT.$SessionID;
	$fileArray = array();
	if(is_dir($dir))
	{
		if( false != ($handle = opendir($dir)) )
		{
			while( false != ($file = readdir($handle)) )
			{
				if( $file!='.' && $file!='..')
				{
					if( false != ($fh = fopen($dir."/".$file, 'r')) )
					{
                        $did = substr($file, 0, strpos($file, '_'));
                        $fileArray[$did][$file]['SessionID']  =   int_long_dest(fread($fh, 4), 0, 4);
					    $fileArray[$did][$file]['DID']        =   int_long_dest(fread($fh, 4), 0, 4);
					    $fileArray[$did][$file]['ret']        =   int_long_dest(fread($fh, 4), 0, 4);
                        if($did == $DID)    /** 避免104心跳检测干扰*/
                        {
                            $fileArray[$did][$file]['data']       =   fread($fh, 8192);
                        }
						fclose($fh);
					}
				}
			}
			closedir( $handle );
		}
		else
		{
			return 0;
		}
        if(DEBUG_MODE)
        {
            echo "<br>file：";
            var_dump ($fileArray);
        }
		return $fileArray;
	}
	else
	{
		return 0;
	}
}

/**
 *  DID通用的获取数据的函数
 */ 
 function did_data($SessionID, $DID)
 {
    $data = NULL;
    if(0 !== checkSessionIDDir($SessionID, $DID))
    {
    	$files = getDIDFileData($SessionID, $DID);
        foreach($files as $k => $v)
        {   
            foreach($v as $k1 => $v1)
            {
                foreach($v1 as $k2 => $v2)
                {
                    if($k2==='ret' && $v2!=='0')  /** 文件返回值判断，成功：0, 失败：其他，错误含义待完成**/
                    {   
                        if(DEBUG_MODE) echo "<br>DID: $k, ERROR: ret in file is $v2<br>";
                        return NULL;
                    }
                    if($k2 === 'data')
                        $data = $v2;
                } 
            }
        }
    }
    return $data;
 }
 
/**
 *  删除SessionID文件 
 *  返回值 成功：NULL 失败: 1
 */
function deleteSessionIDFile($SessionID)
{
    $dir = OMNI_DATA_ROOT.$SessionID.'/';
	$fileArray = array();
	if(is_dir($dir))
	{
		if( false != ($handle = opendir($dir)) )
		{
			while( false != ($file = readdir($handle)) )
			{
                @unlink($dir.$file);
			}
			closedir( $handle );
		}
		else
		{
            if(DEBUG_MODE) echo "opendir : $ret<br>";
			return 1;
		}
		$ret = rmdir($dir);
        if(DEBUG_MODE) echo "rmdir : $ret<br>";
	}
}

 /**
 * *************************** DID functions *****************************
 */

 /*
 *  DID = 104
 *  Event Log Alarm
 *  成功返回二维数组
 *  失败返回 0
 */
 /*
  function did_104($str)
 {
    $outGetEventLog = array();
    
    if(strlen($str) > 0)
    {
        $outGetEventLog['serverId']     =   (int)substr($str, 0, 4);
        $outGetEventLog['count']        =   (int)substr($str, 8, 12);
        for ($i=0; $i<$outGetEventLog['count']; $i++)
        {
            $baseAddr = $i*163 + 12;
            $outGetEventLog['log_'.$i]['Head']      =   (int)substr($str, $baseAddr+0, $baseAddr+4);//4
            $outGetEventLog['log_'.$i]['LogLevel']  =   (int)substr($str, $baseAddr+4, $baseAddr+8);//4
            $outGetEventLog['log_'.$i]['Time']      =   (int)substr($str, $baseAddr+8, $baseAddr+12);//4
            $outGetEventLog['log_'.$i]['LogType']   =   (int)substr($str, $baseAddr+12, $baseAddr+16);//4
            $outGetEventLog['log_'.$i]['ModuleName']=   substr($str, $baseAddr+16, $baseAddr+37);//21
            $outGetEventLog['log_'.$i]['UserName']  =   substr($str, $baseAddr+37, $baseAddr+58);//21
            $outGetEventLog['log_'.$i]['CameraId']  =   (int)substr($str, $baseAddr+58, $baseAddr+62);//4
            $outGetEventLog['log_'.$i]['Content']   =   substr($str, $baseAddr+62, $baseAddr+163);//101
        }
        return $outGetEventLog;
    }
    else    //长度不足
    {
        return 0;
    }
 }
 */
/**
 *  DID = 148
 *  获取nvr服务器信息
 *  成功返回一维数组
 *  失败返回 0
 */
 function did_148($str)
 {
    $nvrServerInfor = array();
    if(strlen($str) > 0)
    {
        $nvrServerInfor['serverId']         =   int_long_dest($str, 0, 8);
        $nvrServerInfor['maximumChannel']   =   int_long_dest($str, 8, 4);
        $nvrServerInfor['major']            =   int_long_dest($str, 12, 4);
        $nvrServerInfor['minor']            =   int_long_dest($str, 16, 4);
		$nvrServerInfor['revision']			=	str_dest($str, 20, 10);
        $nvrServerInfor['serverName']       =   str_dest($str, 30, 80);
        $nvrServerInfor['module']           =   int_long_dest($str, 110, 4);
        return $nvrServerInfor;
    }
    else    //长度不足
    {
        return NULL;
    }
 }
 
 /**
 *  DID = 30
 *  从NVR获取camera list
 *  成功返回数组
 *  失败返回 NULL
 */

 function did_30($str)
 {
    $dataArr = array();
    if(strlen($str) > 0)
    {
        $dataArr['nvrServerId']         =   int_long_dest($str, 0, 8);
        $dataArr['cameraCount']         =   int_long_dest($str, 8, 4);
        for($i=0; $i<$dataArr['cameraCount']; $i++)
        {
            $base = $i*318+12;
            $dataArr['camera'][$i]['CameraId']       =   int_long_dest($str, $base, 4);
            $dataArr['camera'][$i]['CameraIp']       =   int_long_dest($str, $base+4, 4);
            $dataArr['camera'][$i]['MacAddress']     =   str_dest($str, $base+8, 6);
            $dataArr['camera'][$i]['HttpPort']       =   int_long_dest($str, $base+14, 4);
            $dataArr['camera'][$i]['UserName']       =   str_dest($str, $base+18, 20);
            $dataArr['camera'][$i]['Password']       =   str_dest($str, $base+38, 20);
            $dataArr['camera'][$i]['Vendor']         =   str_dest($str, $base+58, 20);
            $dataArr['camera'][$i]['Model']          =   str_dest($str, $base+78, 20);
            $dataArr['camera'][$i]['Description']    =   str_dest($str, $base+98, 128);
            $dataArr['camera'][$i]['CameraName']     =   str_dest($str, $base+226, 64);
            $dataArr['camera'][$i]['Initied']        =   int_long_dest($str, $base+290, 4);
            $dataArr['camera'][$i]['IconId']         =   int_long_dest($str, $base+294, 4);
            $dataArr['camera'][$i]['StreamPort']     =   int_long_dest($str, $base+298, 4);
            $dataArr['camera'][$i]['Subid']          =   int_long_dest($str, $base+302, 4);
            $dataArr['camera'][$i]['Enabled']        =   int_long_dest($str, $base+306, 4);
            $dataArr['camera'][$i]['DvrveId']        =   int_long_dest($str, $base+310, 4);
            $dataArr['camera'][$i]['DvrveCard']      =   int_long_dest($str, $base+314, 4);
            
        }
        return $dataArr;
    }
    else
    {
        return NULL;
    }
 }
 
 /*
 *  DID = 483
 */
 /*
 function did_483($str)
 {
    $dataArr = array();
    if(strlen($str) > 0)
    {
        $dataArr['szModule']    =   str_dest($str, 0, 128);
        $dataArr['nSize']       =   int_long_dest($str, 128, 4);
        $dataArr['nStatus']     =   int_long_dest($str, 132, 4);
        return $dataArr;
    }
    else
    {
        return NULL;
    }
 }
 */
 /**
 *  DID = 185
 *  从NVR获取磁盘信息
 *  成功返回数组
 *  失败返回 0
 */

 function did_185($str)
 {
    $dataArr = array();
    if(strlen($str) > 0)
    {
        $dataArr['count']   =   int_long_dest($str, 0, 4);
        for($i=0; $i<$dataArr['count']; $i++)
        {
            $base = $i*620+4;
            $dataArr['storageInfor'][$i]['enable']              =   int_long_dest($str, $base, 4);
            $dataArr['storageInfor'][$i]['online']              =   int_long_dest($str, $base+4, 4);
            $dataArr['storageInfor'][$i]['szDiskSerialNumber']  =   str_dest($str, $base+8, 64);
            $dataArr['storageInfor'][$i]['diskno']              =   int_long_dest($str, $base+72, 4);
            $dataArr['storageInfor'][$i]['ptno']                =   int_long_dest($str, $base+76, 4);
            $dataArr['storageInfor'][$i]['diskType']            =   int_long_dest($str, $base+80, 4);
            $dataArr['storageInfor'][$i]['drive']               =   str_dest($str, $base+84, 260);
            $dataArr['storageInfor'][$i]['logPath']             =   str_dest($str, $base+344, 260);
            $dataArr['storageInfor'][$i]['raidLevel']           =   int_long_dest($str, $base+604, 4);
            $dataArr['storageInfor'][$i]['dataType']            =   int_long_dest($str, $base+608, 4);
            $dataArr['storageInfor'][$i]['totalsize']           =   int_long_dest($str, $base+612, 4);
            $dataArr['storageInfor'][$i]['freesize']            =   int_long_dest($str, $base+616, 4); 
        }
        return $dataArr;
    }
    else
    {
        return NULL;
    }
 }
 
  /**
 *  DID = 82
 *  从NVR获取camera信息，像素等
 *  成功返回数组
 *  失败返回 0
 */

 function did_82($str)
 {
    $dataArr = array();
    if(strlen($str) > 0)
    {
        $dataArr['nvrServerId']   =   int_long_dest($str, 0, 8);
        $base = 8;
        $dataArr['cameraVideo']['enable']       =   int_long_dest($str, $base, 4);
        $dataArr['cameraVideo']['Resolution']   =   str_dest($str, $base+4, 32);
        $dataArr['cameraVideo']['Fps']          =   str_dest($str, $base+36, 4);
        $dataArr['cameraVideo']['QualityFlag']  =   int_long_dest($str, $base+40, 4);
        $dataArr['cameraVideo']['Quality']      =   str_dest($str, $base+44, 16);
        $dataArr['cameraVideo']['Bitrate']      =   str_dest($str, $base+60, 16);
        
        $name = array(0=>'MPEG4', 1=>'MJPEG', 2=>'H264',);
        for($i=0; $i<3; $i++)
        {   
            $base = $i*2768+84;
            $dataArr['cameraVideo'][$name[$i]]['Enabled']           =   int_long_dest($str, $base, 4);
            $dataArr['cameraVideo'][$name[$i]]['resolutionCount']   =   int_long_dest($str, $base+4, 4);
            $dataArr['cameraVideo'][$name[$i]]['ResolutionList']    =   str_dest($str, $base+8, 512);
            $dataArr['cameraVideo'][$name[$i]]['FpsList']           =   str_dest($str, $base+520, 320);
            $dataArr['cameraVideo'][$name[$i]]['QualityEnabled']    =   int_long_dest($str, $base+840, 4);
            $dataArr['cameraVideo'][$name[$i]]['QualityList']       =   str_dest($str, $base+844, 640);
            $dataArr['cameraVideo'][$name[$i]]['BitrateEnabled']    =   int_long_dest($str, 1484, 4);
            $dataArr['cameraVideo'][$name[$i]]['BitrateList']       =   str_dest($str, $base+1488, 1280);
        }
        return $dataArr;
    }
    else
    {
        return NULL;
    }
 }
 
 ?>
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
