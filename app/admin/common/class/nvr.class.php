<?php
include_once(APP_ROOT.'common/class/omni.class.php');

class nvr
{

    private $obj;
    private $defultServerName = 2809;

    public function __construct ()
    {
        $this->obj = new omni();
    }

    public function __destruct()
    {
        unset($this->obj);
    }
    
    /** 向nvr发送did请求前的初始化*/
    /** 返回值 成功：0  失败：1 */
    public function didInit($nvrServerIP, $nvrServerName, $nvrUser, $nvrPassword, $nvrSessionID)
    {
        $ret = 0;
        if(DEBUG_MODE) echo "nvr $nvrServerIP<br>";
        $ret = $this->obj->connect($nvrServerIP, $nvrServerName);
        if(DEBUG_MODE) echo "connect $ret<br>";
        if($ret !== 0)
        {   
            return 1;
        }
        $ret = 0;
        $ret = $this->obj->login($nvrUser, $nvrPassword);
        if(DEBUG_MODE) echo "login $ret<br>";
        if($ret !== 0)
        {
            return 1;
        }
        
        $ret = true;
        if(is_dir(OMNI_DATA_ROOT.$nvrSessionID))
            deleteSessionIDFile($nvrSessionID);
        $ret = mkdir(OMNI_DATA_ROOT.$nvrSessionID, 0666);
        if($ret === true)
        {
            $sessionID = $this->obj->createSession($nvrSessionID);
            if(DEBUG_MODE) echo "createSession $sessionID<br>";
            return $sessionID;
        }
        else
            return 1;
    }

    /** 发送DID请求，传入参数inInt，传出参数outInt*/
    public function didRequest($DID, $inInt, $outInt)
    {
        $ret = 0;
        $ret = $this->obj->dispatch($DID, $inInt, $outInt);
        if(DEBUG_MODE) echo "dispatch $ret<br>";
        if($ret !== 0)
        {
            return 1;
        }
    }

    /** 检测DID返回文件是否存在，获取文件内容,并删除文件 */
    /** 返回值  成功：array()  失败：1 */
    public function didHarvest($DID, $sessionID)
    {
        $data = NULL;
        $nvrServerInfor = NULL;
        if($sessionID !== 0)
        {
            $data = did_data($sessionID, $DID);
            if($data !== NULL && $data !== '')                          /** 根据DID调用相应的处理函数*/
            {
               switch($DID)
                {
                case 148:   //获取nvr information
                    $nvrServerInfor = did_148($data);
                    break;
                case 30:    //获取nvr camera list
                    $nvrServerInfor = did_30($data);
                    break;
                case 185:   //获取nvr storage information
                    $nvrServerInfor = did_185($data);
                    break;
                case 82:   //获取nvr camera information
                    $nvrServerInfor = did_82($data);
                    break;
                };
            }
            else
            {
                deleteSessionIDFile($sessionID);
                return 1;
            }
        }
        deleteSessionIDFile($sessionID);
        return $nvrServerInfor;
    }
    
    /** DID活动彻底结束，断开连接 */
    public function didDie()
    {
        $ret = $this->obj->disconnect();
        if(DEBUG_MODE) "disconnect $ret<br>";
    }
    
    /** 传入值为空的一次性请求*/
    public function getNvrDataByDomainData($DID, $nvrInfoArray)
    {
        return $this->getNvrDataByDomainDataInt($DID, 0, $nvrInfoArray);
    }

    /** 传入值为整数的一次性请求*/
    public function getNvrDataByDomainDataInt($DID, $inInt, $nvrInfoArray)
    {
        /** 获取 nvr IP*/
        if(is_array($nvrInfoArray))
        {
                foreach($nvrInfoArray as $k1 => $v1)
                {
                    if($k1 === 'name')
                    {
                        //$nvrServerName
                    }
                    else if($k1 === 'ip')
                    {
                        $omniNameServiceIP = $v1;
                    }
                }
        }
        else
        {   
            return 1;   //出错
        }
        /** 获取 nvrServerName */
        $nvrServerName = $this->defultServerName;
        /** 获取 userName password*/
        $userName = 'admin';
        $password = 'admin';
        
        /** 获取 SessionID */
        $nvrSessionID = 123456789;
        
        /** 初始化 */
        $nvrSessionID  = $this->didInit($omniNameServiceIP, $nvrServerName, $userName, $password, $nvrSessionID);
        if($nvrSessionID  === 1)
            return 1;
        
        /** 发送请求 */
        $iInt = $inInt;
        $outInt = 0;
        $ret = $this->didRequest($DID, $iInt, $outInt);
        if($ret === 1)
            return 1;
            
        /** 获取数据*/
        $nvrServerInfor = $this->didHarvest($DID, $nvrSessionID);
        if($nvrServerInfor === 1)
            return 1;
            
        /** 断开连接 */
        //$this->didDie();
        
        return $nvrServerInfor;
    }
/*
    public function getNvrDataByDomainData($DID, $nvrInfoArray)
    {    
        $nvrServerName = 2809;
        $omniNameServiceIP = '';
        $userName = '';
        $password = '';
        
        $sessionID = 0;
        $inInt = 1;
        $outInt = 0;
    

        if(is_array($nvrInfoArray))
        {
                foreach($nvrInfoArray as $k1 => $v1)
                {
                    if($k1 === 'name')
                    {
                        //$nvrServerName
                    }
                    else if($k1 === 'ip')
                    {
                        $omniNameServiceIP = $v1;
                    }
                }
        }
        else
        {   
            if(DEBUG_MODE) echo "$omniNameServiceIP<br>";
            return 1;   //出错
        }
        

        $userName = 'admin';
        $password = 'admin';


        $ret = 0;
        if(DEBUG_MODE) echo "nvr $omniNameServiceIP<br>";
        $ret = C('omni')->connect($omniNameServiceIP, $nvrServerName);
        if(DEBUG_MODE) echo "connect $ret<br>";
        if($ret !== 0)
        {   
            return 1;
        }
        $ret = 0;
        $ret = C('omni')->login($userName, $password);
        if(DEBUG_MODE) echo "login $ret<br>";
        if($ret !== 0)
        {

            return 1;
        }
        $sessionID = C('omni')->createSession($sessionID);
        if(DEBUG_MODE) echo "createSession $sessionID<br>";

        $ret = 0;
        $ret = C('omni')->dispatch($DID, $inInt, $outInt);
        if(DEBUG_MODE) echo "dispatch $ret<br>";
        if($ret !== 0)
        {
            return 1;
        }
        

        $data = NULL;
        $nvrServerInfor = NULL;
        if($sessionID !== 0)
        {
            $data = did_data($sessionID, $DID);
            if($data !== NULL)                
            {
               switch($DID)
                {
                case 148:   //获取nvr information
                    $nvrServerInfor = did_148($data);
                    break;
                case 30:    //获取nvr camera list
                    $nvrServerInfor = did_30($data);
                    break;
                case 185:   //获取nvr storage information
                    $nvrServerInfor = did_185($data);
                    break;
                case 82:   //获取nvr storage information
                    $nvrServerInfor = did_82($data);
                    break;
                };
            }
            else
                return 1;
        }
        
        $ret = 0;
        $ret = C('omni')->disconnect();
        if(DEBUG_MODE) "disconnect $ret<br>";
        if($ret !== 0)
        {
            return 1;
        }
        if($nvrServerInfor !== NULL)
        {
            if(DEBUG_MODE)
            {
                //deleteSessionIDFile($sessionID);
            }
            else
            {
                deleteSessionIDFile($sessionID);
            }      
            return $nvrServerInfor;
        }
        else
            return 1;
        
    }
 */   
 

}