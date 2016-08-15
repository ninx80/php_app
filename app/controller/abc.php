<?php
class Abc_Controller extends Controller{
    public function index(){  
        $this->assign('abc','twodogs');
        $this->display();
    }
}