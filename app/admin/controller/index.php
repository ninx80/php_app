<?php
class Index_Controller extends Controller{
    public function index(){
        $this->display();
    }
    public function resGet(){//这里可以写关于jsonp的内容
        //jsonp
        if(TYPE=='js'){
            $path=APP_ROOT.PATH.'.'.TYPE;
            //v\Res::apion(file_get_contents(APP_ROOT.PATH.'.'.TYPE))->end(200);
            if(file_exists($path))
                echo file_get_contents(APP_ROOT.PATH.'.'.TYPE);
        }
    }
}