<?php
 /**
 *  DIDͨ�õĻ�ȡ���ݽṹ��ĺ���,���Ҵ����ȴ�ʱ��
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
                    if($k2==='ret' && $v2!=='0')  /** �ļ�����ֵ�жϣ��ɹ���0, ʧ�ܣ�����������������**/
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