<?php
class Img_Controller extends Controller{
    public function __construct() {
        $this->mdl=D('Img');
        
        parent::__construct();
    }
    public function edit(){
        $this -> assign('title',__getString('id')==''?'添加':'编辑');
        $this -> assign('id',__getString('id'));
        $this -> assign('method',__getString('id')==''?'post':'put');
        $this -> display();//有这个才回,加入默认加入页面
    }
    public function resGet(){
        $query = $this->mdl->cast($_GET);
        $ids=__getString('ids');
        $query['status']=1;
        if(!empty($ids)){
            $ids=explode(',',$ids);
            $query['_id']=['$in'=>$ids];
        }
        //$id=__getString('_id',__getString('id',__getString('ids')));
        $id=__getString('id','');
        $getalltitle=__getString('getalltitle');
        //echo $id;die;
        if(!empty($id)){//查单个id时************************
            $item = $this->mdl->findByID($id, str_replace([' '],"+", __getString('field')));
            if($getalltitle){
                $alldata=$this->mdl->where([])->limit(0)->find();
                $data=[$item];
                $this->changeName($data,$alldata);
            }
            if (!empty($data)) {
                v\Res::apion($data[0])->end(200);
            } elseif(!empty($item)){
                 v\Res::apion($item)->end(200);
            }
            else {
                v\Res::apion('无数据')->end(400);
            }
        }
        $forselect2=__getString('forselect2');
        if (!empty($forselect2)) {//select2搜索时，索要数据，这里用的field是由field传值决定，field传值为如：+id,title,name,或-id,title,name ***************
            $regsearch = trim(str_replace(' ','+',__getString('regsearch')),'+');
            $fields=str_replace([' '],"+", __getString('field'));
            if(substr($fields,0,1)!='-'){
                $fields.=','.$regsearch;   
            }
            $regsearch = explode('+', $regsearch);
            foreach ($regsearch as $f) {
                if (!empty($query[$f])) {
                    $query[$f] = ['$like' => "%{$query[$f]}%"];
                }
            }
            $this->mdl->where($query)->field($fields);
            $row = Min(intval(__getString('iDisplayLength', 10)), 100);  // 每次不允许超过100条
            $page = Max(array(intval(__getString('page')), 1));
            $start = intval(__getString('iDisplayStart',$row*($page-1)));
            $row = intval(__getString('row',$row));
            if($row==0){
                $this->mdl->limit($row);
                if($page>1)
                    $data=[];
                else
                    $data=$this->mdl->find();
            }
            else{
                $data=$this->mdl->skip($start)->limit($row)->find();
            }
            $alldata=$this->mdl->where([])->limit(0)->find();
            $this->changeName($data,$alldata);
            v\Res::apion(['aaData'=>$data])->end(200);
        }//组装select搜索
        $data = $this->resDataTable($query);//datatable索要数据,这里的field是由datatable列决定 ****************
        v\Res::apion($data)->end(200);
    }
    public function resPut() {
        $data = __put();
        if (!is_array($data) || count($data) < 1) {
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);
        }
        $this->makeData($data);
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
    public function resPost() {
        $data = $_POST;
             
        if (!is_array($data) || count($data) < 1) {
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);
        }
        $this->makeData($data);
        
        if ($this->mdl->sets($data)->check()->can()) {
            $this->mdl->insert();
            //$id = $this->mdl->lastID();
            //$query = ['_id' => ['$in' => [$id]]];
            //v\Res::apion('添加成功')->end(200);
            v\Res::apion(['r'=>'1','mem'=>'success'])->end(200);
        }else
            v\Res::apion(['r'=>'0','mem'=>'数据格式错误'])->end(400);
    }
    private function makeData(&$data){
        if(empty($data['type_id']))
            v\Res::apion(['r'=>'0','mem'=>'请选择类型'])->end(400);
        if(empty($data['imgs'])) return;
        $data['imgs']=explode(',',$data['imgs']);
        $data['size']=explode(',',$data['size']);
        $data['width']=explode(',',$data['width']);
        $data['height']=explode(',',$data['height']);
    } 
    public function resDelete() {
        $id=  __getString('id',__getString('ids'));
        $id=explode(',',$id);
        if(!empty($id) && is_array($id)){
            $query=['_id'=>['$in'=>$id]];
        }
        $data=['status'=>-1];
        if ($this->mdl->sets($data)->check(false)->can()){
            $this->mdl->where($query)->update($data);
            v\Res::apion('修改成功')->end(200);
        }
        v\Res::apion(v\Err::get())->end(400);
        
    }
} 