<?php
 /**
 *  DID通用的获取数据结构体的函数,并且传出等待时间
 */ 
 function did_data2($SessionID, $DID,&$runtime)
 {
    $data = NULL;
    $runtime=checkSessionIDDir($SessionID,$DID);
    if(0 !== $runtime)
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

?>