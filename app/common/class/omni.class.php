<?php
class omni
{
    private $plat;
    private $obj;
    
    public function __construct()
    {
        $this->plat = PLATFORM;
        if($this->plat)
        {
            $this->obj = new COM(OMNI_COM_CLASS);
        }

    }
    
    public function __destruct()
    {
        unset($this->obj);
    }
    
    /**
     * 连接NVR
     * COM参数:
     * [out]long* ret
     * [in]long omniNameServiceIP
     * [in]long nvrServerName
     * php参数
     * $omniNameServiceIP = '172.16.80.160'
     * $nvrServerName = 2809 ，必须是2809
     * 返回值  0 成功，非0 失败
     * 错误码：****************************待完善
     */
    public function connect($omniNameServiceIP, $nvrServerName = 2809)
    {
        if($this->plat)
        {
            $ret = 0;
            $this->obj->Connect($ret, $omniNameServiceIP, $nvrServerName);
            return $ret;
        }
        
    }
    
    /**
     * 登录NVR
     * COM参数:
     * [out]long* ret
     * [in]long userName
     * [in]long password
     * php参数：
     * $userName = 'admin'
     * $password = 'admin'
     * 返回值  0 成功，非0 失败
     * 错误码：****************************待完善
     */
    public function login($userName, $password)
    {
        if($this->plat)
        {
            $ret = 0;
            $this->obj->login($ret, $userName, $password);
            return $ret;
        }
    }
    
    /**
     * 传入或者获取sessionID
     * COM参数:
     * [out]long* sessionID
     * php参数：
     * $sessionID = 0(没有SessionID) | else(有SessionID)
     * 返回值  SessionID
     */
    public function createSession($sessionID = 0)
    {
        if($this->plat)
        {
            $this->obj->CreateSession($sessionID);
            return $sessionID;
        }
    }
    
    /**
     * 发送请求
     * COM参数:
     * [out]long* ret
     * [in]long did
     * [in]long inPtr
     * [in]long inLen
     * [out]long* outPtr
     * [out]long* outLen 
     * php参数：
     * $did = 请求对应的常量
     * $inPtr = 0；未用此参数时，赋值0
     * $outPtr = 0; 未用此参数，赋值0
     * 返回值  0 成功，非0 失败
     * 错误码：****************************待完善
     */
    public function dispatch($did, $inInt, $outInt)
    {
        if($this->plat)
        {
            $ret = 0;
            $inPtr = $inInt;
            $inLen = 4;
            
            //$did = 82;

            if(DEBUG_MODE) echo "did = $did inPtr = $inPtr, inLen = $inLen<br>";

            $outPtr = $outInt;
            $outLen = strlen($outPtr);
            $this->obj->Dispatch($ret, $did, $inPtr, $inLen, $outPtr, $outLen);
            return $ret;
        }
    }
    
    /**
     * 断开连接
     * COM参数:
     * [out]long* ret
     * php参数：
     * 返回值  0 成功，非0 失败
     * 错误码：****************************待完善
     */
    public function disconnect()
    {
        if($this->plat)
        {   
            $ret = 0;
            $this->obj->Disconnect($ret);
            return $ret;
        }
    }
    
}