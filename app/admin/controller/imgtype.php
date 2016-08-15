<?php
class Imgtype_Controller extends Controller{
    public function __construct() {
        $this->mdl=D('ImgType');
        
        parent::__construct();
    }

    public function edit(){
        $this -> assign('title',__getString('id')==''?'添加':'编辑');
        $this -> assign('id',__getString('id'));
        $this -> assign('method',__getString('id')==''?'post':'put');
        $this -> display();//有这个才会加入默认加入页面
    }
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=6&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=1&sSortDir_0=asc&iSortingCols=1&_=1461428474892
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=1&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=desc&iSortingCols=1&_=1461428474896
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=2&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=3&sSortDir_0=asc&iSortingCols=1&_=1461428474897
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=3&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=3&sSortDir_0=desc&iSortingCols=1&_=1461428474898
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=4&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=1&sSortDir_0=asc&iSortingCols=1&_=1461428474899
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=8&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=1&bRegex=false&iSortCol_0=0&sSortDir_0=desc&iSortingCols=1&_=1461432006442
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=13&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=gggg&bRegex=false&iSortCol_0=0&sSortDir_0=desc&iSortingCols=1&_=1461432006447
    //http://n.twodogs.com/admin/imgtype/resGet.json?sEcho=13&iColumns=6&sColumns=%2C%2C%2C%2C%2C&iDisplayStart=0&iDisplayLength=10&mDataProp_0=_id&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=title&sSearch_1=ggg&bRegex_1=true&bSearchable_1=true&bSortable_1=true&mDataProp_2=en&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=true&mDataProp_3=memo&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=true&mDataProp_4=addtime&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=true&mDataProp_5=status&sSearch_5=&bRegex_5=false&bSearchable_5=true&bSortable_5=true&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=desc&iSortingCols=1&_=1461435980503
    public function resGet(){
        $request=__getString('request');
        if($request=='list'){
            $data = $this->getList();
            v\Res::apion($data)->end(200);
        }
        $query = $this->mdl->cast($_GET);$ids=__getString('ids');
        $query['status']=1;
        if(!empty($ids)){
            $ids=explode(',',$ids);
            $query['_id']=['$in'=>$ids];
        }
        if(__getString('lowest')==1){
            $query['lowest']=1;
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
                $this->AdPName($data,$alldata);
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
            $this->AdPName($data,$alldata);
            v\Res::apion(['aaData'=>$data])->end(200);
        }//组装select搜索
        $data = $this->resDataTable($query);//datatable索要数据,这里的field是由datatable列决定 ****************
        $alldata=$this->mdl->where([])->limit(0)->find();
        $this -> AdPName($data['aaData'],$alldata);
        v\Res::apion($data)->end(200);
    }
    public function getList(){
        $query=['status'=>1];
        $result = $this->resDataTable($query);
        $r=$result['aaData'];
        //$r =$this->mdl->limit(0)->find();
        $data=[];
        foreach($r as $k=>$v){
            if(empty($v['pid']) || $v['pid']=='' || $v['pid']=='0'){
                unset($r[$k]);
                $v['children']=$this->test($r,$v['_id']);
                array_push($data,$v);
                //;
            }
        }
        $result['aaData']=$data;
        return $result;
    }
    function test($r,$pid){
        $data=[];
        foreach($r as $k=>$v){

            if($v['pid']==$pid){
                $v['children']=$this->test($r,$v['_id']);
                $data[$v['_id']]=$v;
            }
        }
        return $data;
        
        
    }
    public function resPost() {
        $data = $_POST;
        if(!empty($data['pid'])){
            $parent =$this->mdl->findByID($data['pid']);
            $data['level']=$parent['level']+1;
            $data['ptitle']=$parent['title'];
        }else{
            $data['ptitle']='';
            $data['level']=1;
        }
        $data['lowest']=1;
        if (!is_array($data) || count($data) < 1) {
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);
        }
        if ($this->mdl->sets($data)->check()->can()) {
            $rs = $this->mdl->insert();
            if(is_array($rs) && $rs['ok']==1){
                if(!empty($data['pid'])){
                    $pdata=['lowest'=>0];
                    $this->mdl->where(['_id'=>$data['pid']])->sets($pdata)->update();
                }
                v\Res::apion(['r'=>'1','mem'=>'success'])->end(200);
                
            }
            //$id = $this->mdl->lastID();
            //$query = ['_id' => ['$in' => [$id]]];
            //v\Res::apion('添加成功')->end(200);
            
        }else
            v\Res::apion(['r'=>'0','mem'=>'数据格式不通过'])->end(400);
    }
    public function resPut() {
        $data = __put();
        if (!is_array($data) || count($data) < 1) {
            v\Res::apion(['r'=>'0','mem'=>'没有要新增的数据'])->end(400);
        }
        if(!empty($data['pid'])){
            $parent =$this->mdl->findByID($data['pid']);
            $data['level']=$parent['level']+1;
            $data['ptitle']=$parent['title'];
        }else{
            $data['ptitle']='';
            $data['level']=1;
        }
        $id = isset($data['id'])?$data['id']:'';
        if (empty($id)) {
            v\Res::apion('请输入要更新数据的Id值')->end(400);
        }
        $olddata=$this->mdl->findByID($id);
        if(empty($olddata)){
            v\Res::apion('数据库中不存在改条数据')->end(400);
        }
        unset($data['id']);
        if ($this->mdl->sets($data)->check(false)->can()) {
            $rs=$this->mdl->where(['_id'=>$id])->update();//updateByIDs($ids);
            if($rs){
                if($data['pid']!=$olddata['pid']){//改pid时，调整lowest
                    if(!empty($olddata['pid'])){
                        $pchildren = $this->mdl->where(['pid'=>$olddata['pid']])->find();
                        if(empty($pchildren)){//如果该行的原父级不存在子集那么就将lowest改为1
                            $oldpdata=['lowest'=>1];
                            $this->mdl->where(['_id'=>$olddata['pid']])->sets($oldpdata)->update();
                        }
                    }
                    if(!empty($data['pid'])){
                        $pdata=['lowest'=>0];
                        $this->mdl->where(['_id'=>$data['pid']])->sets($pdata)->update();
                    }
                }
                v\Res::apion(['r'=>'1','mem'=>'success'])->end(200);
                
            }
            v\Res::apion('修改成功')->end(200);
        }else{
            v\Res::apion('失败')->end(400);
        }
    }
    public function resDelete() {
        $id=  __get('id',__get('ids',__post('id')));
        $id=explode(',',$id);
        if(!empty($id) && is_array($id)){
            $query=['_id'=>['$in'=>$id]];
        }
        $data=['status'=>-1];
        if ($this->mdl->sets($data)->check(false)->can()){
            $olddata=$this->mdl->where($query)->find();
            foreach($olddata as $key=>$val){
                if($val['lowest']!=1){
                    v\Res::apion('所选项中含有子类，不能删除！')->end(400);
                }
            }
            $this->mdl->where($query)->update($data);
            $data2=['lowest'=>1];
            $data_2=['lowest'=>0];
            foreach($olddata as $key=>$val){
                $brothor_count =$this->mdl->where(['pid'=>$val['pid'],'status'=>1  ])->count();
                if($brothor_count==0){
                    $this->mdl->where(['_id'=>$val['pid']])->update($data2);
                }else{
                    $this->mdl->where(['_id'=>$val['pid']])->update($data_2);
                }
            }
            v\Res::apion('修改成功')->end(200);
        }
        v\Res::apion(v\Err::get())->end(400);
        
    }
    private function AdPName(&$data,$unions){
        if (!empty($data)) {
            $unions = array_set_key('_id', $unions);
            foreach ($data as $k => $v) {
                if(isset($v['pid']) && $v['pid']!='0'){
                    if(isset($_GET['field']) && $_GET['field']=='title+pid'){
                        $data[$k]['title'] = $this->getParents($v['pid'],$unions).$v['title'];
                    }
                    else{
                        $data[$k]['title']=$v['title'];
                    }
                    $data[$k]['pid'] = trim($this->getParents($v['pid'],$unions),'>');
                }
                else{
                    $data[$k]['title'] =$v['title'];
                    $data[$k]['pid'] ='空';
                }
            }
        }
    }
    
    private function getParent($pid,$unions){
        $str='';
        if(empty($pid) || $pid=='' || $pid=='0'){
            return '';
        }
        foreach($unions as $k=>$v){
            if($pid==$k){
                return $v['title'];
            }
        }
    }
    private function getParents($pid,$unions){
        $str=''; 
        if(empty($pid) || $pid=='' || $pid=='0'){
            return '';
        }
        foreach($unions as $k=>$v){
            if($pid==$k){
                if(!empty($v['pid']) && $v['pid']!='0')
                    $str.=$this->getParents($v['pid'],$unions).$v['title'].'>';
                else 
                    $str.=$v['title'].'>';
                break;
            }
        }
        return $str;
    }
    private function getAllTitle($item){
        if($item['pid']==''){
            return '';
        }else{
            $parent = $this->mdl->findById($item['pid']);
            $p= $this->getAllTitle($parent);
            return ($p==''?'':($p.'->')).$parent['title'].'->';
        }
    }
} 