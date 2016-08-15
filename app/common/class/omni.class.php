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
     * ����NVR
     * COM����:
     * [out]long* ret
     * [in]long omniNameServiceIP
     * [in]long nvrServerName
     * php����
     * $omniNameServiceIP = '172.16.80.160'
     * $nvrServerName = 2809 ��������2809
     * ����ֵ  0 �ɹ�����0 ʧ��
     * �����룺****************************������
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
     * ��¼NVR
     * COM����:
     * [out]long* ret
     * [in]long userName
     * [in]long password
     * php������
     * $userName = 'admin'
     * $password = 'admin'
     * ����ֵ  0 �ɹ�����0 ʧ��
     * �����룺****************************������
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
     * ������߻�ȡsessionID
     * COM����:
     * [out]long* sessionID
     * php������
     * $sessionID = 0(û��SessionID) | else(��SessionID)
     * ����ֵ  SessionID
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
     * ��������
     * COM����:
     * [out]long* ret
     * [in]long did
     * [in]long inPtr
     * [in]long inLen
     * [out]long* outPtr
     * [out]long* outLen 
     * php������
     * $did = �����Ӧ�ĳ���
     * $inPtr = 0��δ�ô˲���ʱ����ֵ0
     * $outPtr = 0; δ�ô˲�������ֵ0
     * ����ֵ  0 �ɹ�����0 ʧ��
     * �����룺****************************������
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
     * �Ͽ�����
     * COM����:
     * [out]long* ret
     * php������
     * ����ֵ  0 �ɹ�����0 ʧ��
     * �����룺****************************������
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