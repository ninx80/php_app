<?php
include_once(APP_ROOT.'common/class/omni.class.php');

class nvr2
{

    private $obj;
    private $defultServerName = 2809;
    private $username='admin';
    private $password='admin';
    private $omni=array();
    public function getNvrDataByDomainDatas($DID, $nvrInfoArray,$username='admin',$password='admin')
    {
        $nvrServerName = 2809;
        $omniNameServiceIP = array();
        $sessionID = array();
        $inInt = 0;
        $outInt = 0;
        /** 获取 ip*/
        if(is_array($nvrInfoArray))
        {
            if(count($nvrInfoArray)==count($nvrInfoArray,1))
                $nvrInfoArray=array($nvrInfoArray);
            foreach($nvrInfoArray as $key => $val)
            {
                $omniNameServiceIP[] = $val['ip'];
            }
        }
        else
        {   
            return 'parameter $omniNameServiceIP error';   //出错
        }
        /** 开始获取camera数据 */
        $ret = 0;
        $omni=array();
        $i=0;
        foreach($omniNameServiceIP as $single_ip)
        {
            
            $omni[$i]=new omni();
            $ret = $omni[$i]->connect($single_ip, $nvrServerName);
            if($ret==0)
            {
                $ret = $omni[$i]->login($username, $password);
                if($ret == 0)
                {
                    if($_session =$omni[$i]->createSession(0))
                            $sessionID[]=$_session;
                        else
                            $sessionID[]=0;
                    if($_session!=0)
                    {
                         $ret = $omni[$i]->dispatch($DID, $inInt, $outInt);
                    }
                    else
                        $sessionID[]=0; 
                }
                else
                    $sessionID[]=0; 
            }
            else
               $sessionID[]=0;  
        }
        foreach($sessionID as $single_session)
        {
            $data='';
            $nvrServerInfor='';
            if($single_session !== 0)
            {
                $runtime=0;
                $data = did_data2($single_session, $DID,$runtime);
                $GLOBALS['TIME_OUT']=$GLOBALS['TIME_OUT']-$runtime;
                $GLOBALS['TIME_OUT']=$GLOBALS['TIME_OUT']<500?500:$GLOBALS['TIME_OUT'];
                if($data != NULL && $data != '')                          /** 根据DID调用相应的处理函数*/
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
                    };
                    $return[]=$nvrServerInfor;
                }
                else
                    $return[]='';
            }
            else
                $return[]='';
        }
        foreach($omni as $obj)
        {
            $obj->disconnect();
            $obj->__destruct();
        }
        foreach($sessionID as $val)
            deleteSessionIDFile($val);
        return $return;
    } 
    public function doDIDAndGetSessionID($ip,$DID,$inInt=0,$outInt=0)//调用did，创建自定义sessionID
    {
        $omni=new omni();
        
        $ret = $omni->connect($ip, $this->defultServerName);//应该加超时1秒,保证成功，不成功就“做相应的处理，die，或返回空，后面再做”
        if($ret!=0) {echo'链接失败';die; return array(0,0);}
        $ret = $omni->login($this ->username, $this ->password);//应该加超时1秒,,保证成功
        if($ret!=0) {echo'登陆失败';die; return array(0,0);}
        if(isset($GLOBALS['nvrSessionID']))
        {
            do{
                $sid=(int) ('1'.substr(microtime(),2,6).rand(10,99));
            }while(in_array($sid,$GLOBALS['nvrSessionID']));   
        }
        $omni->createSession($sid);
    
        $ret = $omni->dispatch($DID, $inInt, $outInt);//这里omni应该会返回成功标志，这里改加超时
                  
        if($ret!=0) {echo'执行失败';die; return array(0,0);}
        $GLOBALS['nvrSessionID'][]=$sid;
        $this->omni[]=$omni;
        return array($sid,$DID);
    }
    public function getCameraByDIDS($DIDS=array(),$ip,$inInt=array(),$outInt=0)//用于单个ip，多个did请求
    {
    
        if(!CHECK_ARRAY($DIDS))
            return '$DIDS error';
        $sessionAndDID=array();
        foreach($DIDS as $key=> $val)
        {
             $sessionAndDID[]=$this-> doDIDAndGetSessionID($ip,$val,$inInt[$key]);
        }
        return $sessionAndDID;
    }
    public function getdata($DIDS=array(),$ip,$inInt=0,$outInt=0)
    {
        //$sessionAndDID=array(array(sid,did),array(sid,did),array(sid,did));
        $sessionAndDID= $this->getCameraByDIDS($DIDS,$ip,$inInt);
        
        $return=array();
        if(CHECK_ARRAY($sessionAndDID))
        {
            foreach($sessionAndDID as $val)
            {
                $data='';
                $nvrServerInfor='';  
                                          
                if($val[0] != 0)
                {
                    $runtime=0;
                  
                    $data = did_data2($val[0],$val[1],$runtime);

        
                    $GLOBALS['TIME_OUT']=$GLOBALS['TIME_OUT']-$runtime;
                    $GLOBALS['TIME_OUT']=$GLOBALS['TIME_OUT']<500?500:$GLOBALS['TIME_OUT'];
        
                    if($data !==NULL)                          /** 根据DID调用相应的处理函数*/
                    {
                       switch($val[1])
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
                        $return[]=$nvrServerInfor;
                    }
                    else
                        $return[]='';
                }
                else
                    $return[]='';
            }
        }
        foreach($this ->omni as $obj)
        {
            $obj->disconnect();
            $obj->__destruct();
        }
        foreach($sessionAndDID as $val)
            deleteSessionIDFile($val[0]);
        foreach($sessionAndDID as $val)
            deleteSessionIDFile($val[0]);
        return $return;
    }

}