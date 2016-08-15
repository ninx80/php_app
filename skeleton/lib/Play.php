<?php
namespace v;
class play
{
    private static $act=array();
    private static $view='';
    private static $hash='';
    private static $content='';
    
    public static function display($view,$act=array()){
        static::$act=array_merge(static::$act,$act);
        static::$view=$view;
        static::$hash=md5($view);
        if(!file_exists(static::$view))
            die;
        static::parseCss();
        static::parseJs();
        static::getView();
    }
    /**
     * 想要载入默认css,通过这个写上，就在页面写上{$base_css},一般的css用{$css},去除原版中的合并
    **/
    private static function  parseCss(){
        $base_css_href='';
        $css_href='';
        if(isset(static::$act['css_base_load']) && static::$act['css_base_load']==1){
            $b_css=CONF('BASE_CSS');
            if(!empty($b_css)){
                foreach($b_css as $v)
                    $base_css_href.=$v.',';
                $base_css_href=substr($base_css_href,0,-1);
                $base_css='<link media="screen" rel="stylesheet" type="text/css" href="'.$base_css_href.'" />'."\r\n";
                static::$act['base_css']=$base_css;
            }
        }
        
        $inc_css=!empty(static::$act['inc_css'])?static::$act['inc_css']:[];
        if(!empty($inc_css) && is_array($inc_css)){
            foreach($inc_css as $val)
                $css_href.=$val.',';
            $css_href=substr($css_href,0,-1);
            $css='<link media="screen" rel="stylesheet" type="text/css" href="'.$css_href.'" />'."\r\n";
            static::$act['css']=$css;
        }
        unset($base_css,$css);
    }
    /**
     *可由js_base_load来控制，是否加入默认js
    **/  
    private static function parseJs(){
    	$base_js_src='';
        $js_src='';
        if(isset(static::$act['js_base_load']) && static::$act['js_base_load']>0){
            $b_js=CONF('BASE_JS');
            if(!empty($b_js)){
                foreach($b_js as $v)
                    $base_js_src.=$v.',';
                $base_js_src=substr($base_js_src,0,-1);
                $base_js='<script type="text/javascript" src="'.$base_js_src.'"></script>'."\r\n";
                static::$act['base_js']=$base_js;
            }
        }
        $inc_js=!empty(static::$act['inc_js'])?static::$act['inc_js']:[];
        if(!empty($inc_js) && is_array($inc_js)){
            foreach($inc_js as $val){
                $js_src.=$val.',';
            }
            $js_src=substr($js_src,0,-1);
            $js='<script type="text/javascript" src="'.$js_src.'"></script>\r\n';
            static::$act['js']=$js;
        }
        unset($base_js,$js);
    }
    private static function getView(){
        if(CONF('STATIC_CACHE')){
            if(!file_exists(APP_ROOT.'cache/'.static::$hash.'.html') || CONF('DEBUG')){//如果没有view缓存或调试模式就替换变量
               static::$content=file_get_contents(static::$view);
               static::parseSpecialTags();
               file_put_contents(APP_ROOT.'cache/'.static::$hash.'.html',static::$content);
            }
            extract(static::$act);
            include(APP_ROOT.'cache/'.static::$hash.'.html');
        }
        else{
            static::$content=file_get_contents(static::$view);
            static::parseSpecialTags2();
            echo static::$content;
        }
        static::$content='';
    }
    /**
    css压缩函数,简单的去空格/换行/注释
    **/
    public static function compress($buffer){
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);  
        $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);  
        return $buffer;
    }
    /**
    替换view中变量,暂时只支持变量替换,如果要扩展,在这里添加
    **/  
    private static function parseSpecialTags(){
        static::$content = preg_replace('/{\$([^}{\(\)\.]+)}/is', "<?php echo isset($$1) ? $$1 :''; ?>", static::$content);
    }
    private static function parseSpecialTags2(){
        static::$content = preg_replace_callback(
            '/{\$([^}{\(\)\.]+)}/is',
            function ($matches) {
                return static::$act[$matches[1]];
            },static::$content
        );
    }
}