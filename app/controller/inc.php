<?php
class Inc_Controller extends Controller{
    public function header(){
       //这里禁用display方法
        $this->assign(['js_base_load'=>'1','css_base_load'=>'1']);
    }
    public function left(){
        //这里禁用display方法
        
    }
    public function footer(){
        //这里禁用display方法
        
    }
}