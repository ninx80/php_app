<?php
    
/**
*   开启模拟线程任务
*/
function runThread($threadPath)
{
    $fp = fsockopen(DOMAIN_IP, WEB_SERVER_PORT, $errno, $errmsg);
    fputs($fp, "GET /?a=$threadPath.$threadPath\r\n");
    fclose($fp);
}

/**
*   数组转化成xml字符串
*/
function arr2xmlStr($arr,$dom=0,$item=0)
{
    if (!$dom){
        $dom = new DOMDocument("1.0");
    }
    if(!$item){
        $item = $dom->createElement("root"); 
        $dom->appendChild($item);
    }
    foreach ($arr as $key=>$val){
        $itemx = $dom->createElement(is_string($key)?$key:"item");
        $item->appendChild($itemx);
        if (!is_array($val)){
            $text = $dom->createTextNode($val);
            $itemx->appendChild($text);
            
        }else {
            arrtoxml($val,$dom,$itemx);
        }
    }
    return $dom->saveXML();
}
/**
*   xml字符串转化成数组
*/
function xmlStr2arr($xmlStr)
{
    return json_decode(json_encode((array)simplexml_load_string($xmlStr)), true);
}

/**
*   xml文件转化成数组
*/
function xmlFile2arr($xmlFile)
{
    return json_decode(json_encode((array)simplexml_load_file($xmlFile)), true);
}






