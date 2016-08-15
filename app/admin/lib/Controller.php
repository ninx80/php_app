<?php
class Controller extends v\BaseController{
    public static $allow_method=['index','list','edit','resGet','resPost','resPut','resDelete'];
    protected $mdl;
    public function __construct(){
        parent::__construct();
    }
    function __call($func_name, $args){
        $this->display();//页面访问采用restful方式，但支持自定义访问方法，方便页面前置数据载入
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
    public function resDataTable($querys=[]){//得到专门用于datables的数据
        $fields=[];
        $mdl=$this->mdl;
        if (__getString('iColumns')) {
            $mdl_fields = $mdl->fields(false, false);
            for ($i = 0; $i < __getString('iColumns'); $i++) {
                $f = __getString('mDataProp_' . $i);
                if ($f === '')
                    continue;
                $fields[] = $f;
                if (__getString('sSearch_' . $i) != '' && !isset($querys[$f])) {
                    $s = __getString('sSearch_' . $i);
                    $querys[$f] = __getString('bRegex_' . $i) == 'true' ? array('$like' => '%' . $s . '%') : ($mdl_fields[$f][0][0] == 'intval' ? (int) $s : $s);
                }
            }
        }
        //组装select搜索
        $sort = __getString('sort');
        if (__getString('iSortCol_0') != '' && !empty($_GET['mDataProp_'.__getString('iSortCol_0')])) {
            $sort = (__getString('sSortDir_0', 'desc') == 'desc' ? '-' : '') . $_GET['mDataProp_'.__getString('iSortCol_0')];
        }
        //echo __getString('sSortDir_0', 'desc');die;
        if (!is_null($querys))
            $mdl->where($querys);
        $mdl->sort($sort)->field($fields);
        //print_r($mdl->field);
  
        // 分页
        $row = Min(intval(__getString('iDisplayLength', 10)), 100);  // 每次不允许超过100条

        $page = Max(array(intval(__getString('page')), 1));
        $start = intval(__getString('iDisplayStart',$row*($page-1)));
        $row = intval(__getString('row',$row));
        if($row==0){
            $mdl->limit($row);
            if($page>1)
                $items=[];
            else
                $items=$mdl->find();
        }
        else{
            $mdl->skip($start)->limit($row);
            $items=$mdl->find();
        }
        
        $count = $mdl->limit(0)->count();
        //print_r($mdl->field);
        $data = [
            'query' => $querys,
            'field' => $mdl->field,
            'sort' => $sort,
            'count' => $count,
            'aaData' => $items,
            //"draw" => 2,
            "recordsTotal" => $count,
            "recordsFiltered" => $count,
        ];
        return $data;
    }
    public function resPost() {
        $data = $_POST;
        if (!is_array($data) || count($data) < 1) {
            
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);

        }
        if ($this->mdl->sets($data)->check()->can()) {
            $this->mdl->insert();
            //$id = $this->mdl->lastID();
            //$query = ['_id' => ['$in' => [$id]]];
            //v\Res::apion('添加成功')->end(200);
            v\Res::apion(['r'=>'1','mem'=>'success'])->end(200);
        }else
            v\Res::apion(['r'=>'0','mem'=>'数据格式不通过'])->end(400);
    }
    public function resPut() {
        $data = __put();
        if (!is_array($data) || count($data) < 1) {
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);
        }
        $ids = isset($data['id'])?$data['id']:(isset($data['ids'])?$data['ids']:(isset($data['_id'])?$data['_id']:''));
        if (empty($ids)) {
            v\Res::apion('请输入要更新数据的Id值')->end(400);
        }
        $ids = array_unique(explode(',', $ids));
        unset($data['id'], $data['_id'], $data['ids']);
        if ($this->mdl->sets($data)->check(false)->can()) {
            $this->mdl->where(['_id'=>['$in'=>$ids]])->update();//updateByIDs($ids);
            v\Res::apion('修改成功')->end(200);
        }else{
            v\Res::apion('失败')->end(400);
        }
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