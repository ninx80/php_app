<?php
class Index_Controller extends Controller{
    public function index(){
        $data=['username'=>'nixun','realname'=>'ad','password'=>'111','groupid'=>'ad121'];
        $mdl=D('AdminUser');
        if($mdl->sets($data)->check()->checkUsername()->can()){
            
        }//$a=$mdl->insert($data); 
       $this->display();
    }
} 