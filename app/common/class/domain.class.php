<?php
class domain
{
    private $plat;
    private $obj;
    
    private $ip;
    private $port;
    private $nmsgport;

    public function __construct ()
    {
        $this->plat = PLATFORM;
        if($this->plat)
        {
            $this->obj = new COM(DOMAIN_COM_CLASS);
            $this->ip = DOMAIN_IP;
            $this->port = DOMAIN_PORT;
            $this->nmsgport = DOMAIN_NMSPORT;
        }
    }

    public function __destruct()
    {
        unset($this->obj);
    }

    public function ucTrylogin($name, $pwd)
    {
        if($this->plat)
        {
            $result = $this->obj->UC_Trylogin($this->ip, $this->port, $this->nmsgport, $name, $pwd);
            return $result;
        }
    } 
    
    public function ucGetAllUsers()
    {
        if($this->plat)
        {   
            $infos = '';    //返回XML数据
            $result  = $this->obj->UC_GetAllUsers($this->ip, $this->port, $infos, $bufflen=0, $clienttype=1);
            if($result >= 0)
            {
                if($infos !== '')
                {
                    $xml_array = json_decode(json_encode((array)simplexml_load_string($infos)), true);
                    return $xml_array;
                }
                else
                {
                    return -2;
                }
            }
            else
            {
                return -1;
            }
        }   
    }

    public function ucGetOnlineUsers()
    {
        if($this->plat)
        {   
            $infos = '';    //返回XML数据
            $result  = $this->obj->UC_GetOnlineUsers($this->ip, $this->port, $infos, $bufflen=0);
            if($result > 0)
            {
                if($infos !== '')
                {
                    $xml_array = json_decode(json_encode((array)simplexml_load_string($infos)), true);
                    return $xml_array;
                }
                else
                {
                    return -2;
                }
            }
            else
            {
                return -1;
            }
      }
    }
    
    public function getNvrsInfo()
    {
        if($this ->plat)
        {
            $infos = '';  //返回XML数据
            $result  = $this->obj->UC_GetNvrsInfo($this->ip, $this->port, $infos, $nlen=0, $DomainType=1);
            if($result > 0)
            {
                if($infos !== '')
                {
                    $xml_array = json_decode(json_encode((array)simplexml_load_string($infos)), true);
                    return $xml_array;
                }
                else
                {
                    return -2;
                }
            }
            else
            {
                return -1;
            }
        } 
    }

}










