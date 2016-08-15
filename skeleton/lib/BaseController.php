<?php
namespace v;
class BaseController
{
    public $act=array();
    public $auto_f_inc=[];
    public $auto_l_inc=[];
    public $auto_f_inc_controller=[];
    public $auto_l_inc_controller=[];
    public static $allow_method=['index','list','edit','resGet','resPost','resPut','resDelete'];//暂时没用到，以后加
    public function __construct(){
        if(!in_array(METHOD,static::$allow_method))
            Res::apion('禁止访问')->end(400);
        $this->auto_f_inc=CONF('AUTO_FIRST_INC_TPL');
        $this->auto_l_inc=CONF('AUTO_LAST_INC_TPL');
    }
    /**
     * 支持assign(['a'=>1,'b'=>4])表示$a=1;$b=4
     * inc_js 和inc_css是加入js和css，调用的话是在view中用{$js}{$css}
    **/
    public function assign($key,$val=''){
        if(is_array($key) && !empty($key)){
            foreach($key as $k=>$v){
                if(0!==strcmp($k,(int)$k))
                    $this->act[$k]=$v;
            }
        }elseif(is_string($key))
            $this->act[$key]=$val;
        return $this;
    }
/**
 * 该display 方法主要针对实现加载默认页面
默认ajax不会去渲染页面的，所以向渲染页方法中加入display,但且为html后缀，是加入display又会把全部页面加入，所以加入判断
    1、ajax-》ajax不要页面,只要数据时，要求 请求的后缀为.json，方法中display可用可不用
    2、ajax-》ajax要单个页面，后缀为html,display可有可无
    3、ajax-》要全部页面    暂时没用到，不过可以做到，比如，加加一个传值，在diplay中加入判断
    5、非ajax-》不要页面，只要数据    若为后缀.json则不渲染，这个方式一般用于调试时候，查看json详情用
    5、非ajax-》要单个页面  后缀用html，控制器中不用display
    6、非ajax-》要全部页面  后缀为.html，controller中加入$this->display()
 * 
 * 1、不用display时，不会加载默认载入页面，这时，请求的只与后缀有关，html则渲染，json则不渲染
 * 2、用display时，首先
 * （1）判断后缀，为json，则不渲染，所以就只有数据；
 * （2）为html则渲染，至于渲染成什么，看是否为ajax，如为ajax请求只渲染单个页面，非ajax请求时渲染全部页面
 * 想要附加的页面必须用display
 */   
    public function display($view='',$act=[]){
        $act=empty($act)?$this->act:$act;        
        if(TYPE=='html'){
            if(!empty($this->auto_f_inc) && !isAjax()){
                foreach($this->auto_f_inc as $key=>$val){
                    $this->auto_f_inc_controller[$key] = C($val['c']);
                    $this->auto_f_inc_controller[$key]->$val['m']();
                    v\play::display( APP_ROOT.'view/'.$val['c'].'/'.$val['m'].'.html',$this->auto_f_inc_controller[$key]->act);
                    unset($this->auto_f_inc_controller[$key]);
                }
            }
            $view = $view == '' ? (APP_ROOT.'view/'.CONTROLLER.'/'.METHOD.'.html'):(APP_ROOT.'view/'.$view);
            $play=v\play::display($view,$act);
            if(!empty($this->auto_l_inc) && !isAjax()){
                foreach($this->auto_l_inc as $key=>$val){
                    $this->auto_l_inc_controller[$key] = C($val['c']);
                    $this->auto_l_inc_controller[$key]->$val['m']();
                    v\play::display( APP_ROOT.'view/'.$val['c'].'/'.$val['m'].'.html',$this->auto_l_inc_controller[$key]->act);
                    unset($this->auto_l_inc_controller[$key]);
                }
            }
         else{
            $view = $view == '' ? (APP_ROOT.'view/'.CONTROLLER.'/'.METHOD.'.html'):(APP_ROOT.'view/'.$view);
            $play=v\play::display($view,$act);
                unset($this->auto_l_inc_controller[$key]);
            }
        }
        die;
    }
}