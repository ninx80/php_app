<?php
class Controller extends v\BaseController{
    public function __construct(){
        parent::__construct();
    }
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
    public function resGet(){
        $query=$this->mdl->cast($_GET);
        $fields=str_replace([' '],"+", __getString('field'));
        $regsearch = trim(str_replace(' ','+',__getString('regsearch')),'+');
        if ($regsearch) {
            if(substr($fields,0,1)!='-'){
                $fields.='+'.$regsearch;   
            }
            $regsearch = explode('+', $regsearch);
            foreach ($regsearch as $f) {
                if (!empty($query[$f])) {
                    $query[$f] = ['$like' => "%{$query[$f]}%"];
                }
            }
        }//组装select搜索
        $sort = __getString('sort');
        if (__getString('iSortCol_0') != '' && !empty($_GET['mDataProp_'.__getString('iSortCol_0')])) {
            $sort = (__getString('sSortDir_0', 'desc') == 'desc' ? '-' : '') . $_GET['mDataProp_'.__getString('iSortCol_0')];
        }
        //echo __getString('sSortDir_0', 'desc');die;
        $this->mdl->where($query);
        $this->mdl->sort($sort)->field($fields);
        //print_r($this->mdl->field);
  
        // 分页
        $row = Min(intval(__getString('iDisplayLength', 10)), 100);  // 每次不允许超过100条

        $page = Max(array(intval(__getString('page')), 1));
        $start = intval(__getString('iDisplayStart',$row*($page-1)));
        $row = intval(__getString('all',$row));
        $this->mdl->skip($start)->limit($row);
        $items=$this->mdl->find();
        $count = $this->mdl->limit(0)->count();
        //print_r($this->mdl->field);
        $data = [
            'query' => $query,
            'field' => $this->mdl->field,
            'sort' => $sort,
            'count' => $count,
            'aaData' => $items,
            //"draw" => 2,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
        ];
        echo json_encode($data);
    }
}
/**
默认ajax不会去渲染页面的，所以向渲染页方法中加入display,但且为html后缀，是加入display又会把全部页面加入，所以加入判断
    1、ajax-》ajax不要页面,只要数据，后缀为.json，display可有可无
    2、ajax-》ajax要单个页面，后缀为html,display可有可无
    3、ajax-》要全部页面    暂时没用到，不过可以做到，比如，加加一个传值，在diplay中加入判断
    5、非ajax-》不要页面    若为后缀.json则不渲染
    5、非ajax-》要单个页面  html，不用display就可以
    6、非ajax-》要全部页面  非ajax，后缀为.html，controller中加入$this->display()
 * 
 * 1、不用display时，不会加载默认载入页面，这时，请求的只与后缀有关，html则渲染，json则不渲染
 * 2、用display时，首先
 * （1）判断后缀，为json，则停止渲染；
 * （2）为html则渲染，至于渲染成什么，用ajax判断，ajax请求只渲染单个页面，非ajax请求时渲染全部页面
 * 想要附加的页面必须用display
 */