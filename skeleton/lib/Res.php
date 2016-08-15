<?php

/*
 * 框架响应对象
 */

namespace v;
//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));
final class Res extends Fac {

    /**
     * 服务提供对象名
     * @var string
     */
    protected static $objname;

    /**
     * 服务提供对象
     * @var object
     */
    protected static $object;

}

/**
 * 响应服务类
 * 用户的响应类从此继承
 */
define('vResHookRESPONSED', 1);  // 数据响应
define('vResTRANSLATE', 1);  // 翻译数据
define('vResORIGINAL', 0); // 不翻译数据

class ResSvc extends Svc {

    /**
     * http 头
     * @var array
     */
    protected $headers = [];

    /**
     * 响应正文
     * @var string
     */
    protected $body = '';

    /**
     * 跨域jsonp函数
     * @var string
     */
    protected $jsonp_callfn = null;

    /**
     * 最后响应状态
     * @var string
     */
    protected $status = 200;

    /**
     * 字符编码
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * 编码
     * @var string
     */
    protected $type = 'txt';

    /**
     * 配置
     * @var array
     */
    protected $configs = array(
        'types' => array(
            'html' => 'text/html',
            'xml' => 'text/xml',
            'json' => 'application/json',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'ico' => 'image/x-icon',
            'swf' => 'application/x-shockwave-flash',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'otf' => 'application/x-font-otf',
            'eot' => 'application/octet-stream',
            'woff' => 'application/x-font-woff',
            'svg' => 'image/svg+xml',
            'ttf' => 'application/octet-stream',
            'csv' => 'text/csv',
            'file' => 'application/octet-stream'
        )
    );

    /**
     * 初始化设置系统编码
     */
    public function __construct() {
        $charset=CONF('charset');
        parent::__construct();
        $this->charset( empty($charset)?$this->charset:$charset ); //$this->charset
        $this->type = TYPE;
    }

    /**
     * 输出http头，同php header
     * @param string $string
     * @param boolean $replace
     * @param int $http_response_code
     */
    public function header($string, $replace = true, $http_response_code = null) {
        if (strpos($string, 'Status:') === 0)
            $string = str_replace('Status', $_SERVER['SERVER_PROTOCOL'], $string);
        $this->headers[] = [$string, $replace, $http_response_code];
        return $this;
    }

    /**
     * 响应状态码
     * @param string|int $no
     */
    public function status($no=null, $string = null) {
        if(empty($no)){
            return $this->status;
        }
        $this->status = $no;
        return $this;
    }

    /**
     * 响应类型
     * @param string $type
     */
    public function type($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * 字符编码
     * @param string $charset
     * @return \v\ResSvc
     */
    public function charset($charset) {
        $this->charset = $charset;
        return $this;
    }

    /**
     * 响应字符串
     * @param string $text
     */
    public function text($string, $translate = true) {
        $this->type('txt');
        $this->body = $translate ? App::t($string) : $string;
        return $this;
    }

    /**
     * 响应html
     * @param string $string
     */
    public function html($string, $translate = true) {
        $this->type('html');
        $this->body = $translate ? App::t($string) : $string;
        return $this;
    }

    /**
     * 响应xml
     * @param array|string $data
     * @return string
     */
    public function xml($data, $translate = true) {
        $this->type('xml');
        if ($translate)
            $data = App::t($data);
        if (!is_array($data))
            return substr($data, 0, 5) == '<?xml' ? $data : "<?xml version='1.0' encoding='{$this->charset}'?><data>" . htmlentities($data, null, $this->charset) . '</data>';

        $xml = simplexml_load_string("<?xml version='1.0' encoding='{$this->charset}'?><data />");
        $fun = function($v, $k, $xml) use (&$fun) {
            (is_int($k) || ctype_digit(substr($k, 0, 1))) and $k = "d$k";
            if (is_array($v)) {
                $node = $xml->addChild($k);
                array_walk($v, $fun, $node);
            } else {
                $xml->addChild($k, htmlentities($v, null, $this->charset));
            }
        };
        array_walk($data, $fun, $xml);
        $this->body = $xml->asXML();
        return $this;
    }

    /**
     * 响应json
     * @param string $data
     */
    public function json($data, $translate = true) {
        $this->type('json');
//        if ($translate)
//            $data = App::t($data);
        $this->body = json_encode($data,JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * 响应json或xml的api格式
     * @param array $data
     */
    public function apion($data, $translate = true) {
        $type = $this->type;
        if (in_array($type, ['json', 'xml'])) {
            $fn = $type === 'js' ? 'json' : $type;
            $this->$fn($data, $translate);
        } elseif (is_string($data)) {
            $this->body = $data;
        } else {
            $this->end(406, 'Not Acceptable');
        }
        return $this;
    }

    /**
     * 响应视图
     * @param string $file
     * @param array $data
     */
    public function view($file, $data = null) {
        $this->body = Vew::view($file, $data);
        return $this;
    }

    /**
     * jsonp响应
     * @param string $var
     */
    public function withJsonp($var) {
        if (!empty($_GET[$var]))
            $this->jsonp_callfn = GET($var);
        return $this;
    }

    /**
     * 设置|取得响应正文
     * @param string $string
     * @return string
     */
    public function body($string = null) {
        if (is_null($string))
            return $this->body;
        $this->body = $string;
        return $this;
    }

    /**
     * 向浏览器输出响应
     * @param int $status 响应状态
     */
    public function end($status = null, $string = null) {
        if (!empty($status))
            $this->status($status, $string);
        // 类型响应
        $this->headers[] = ["{$_SERVER['SERVER_PROTOCOL']}: {$this->status}"];
        if (isset($this->configs['types'][$this->type])) {
            $chars = ['txt' => 1, 'json' => 1, 'xml' => 1, 'html' => 1];
            $this->headers[] = ["Content-Type: {$this->configs['types'][$this->type]}" . (isset($chars[$this->type]) ? "; charset={$this->charset}" : '')];
        }
        // 响应header
        foreach ($this->headers as $param) {
            call_user_func_array('header', $param);
        }

        // 格式化数据
        $format_fn = 'pack2' . ucfirst($this->type);
        if (method_exists($this, $format_fn)) {
            $this->$format_fn();
        }

        // 最后响应的数据HOOK
        if ($this->triHook(vResHookRESPONSED, $this->body))
            return;

        if ($this->jsonp_callfn) {
            // 响应JSONP
            echo "{$this->jsonp_callfn}({$this->body}, {$this->status})";
        } else {
            // 响应数据
            echo $this->body;
        }
        
        exit;
    }

    /**
     * 重定向
     * @param string $url
     * @param int $status
     */
    public function redirect($url, $status = 302) {
        $this->header("Location: $url")->end($status);
    }

    /**
     * 设置cookie
     * @param string $name
     * @param string $value 为0或false时删除cookie
     * @param int $expire 过期时间，秒。如60
     */
    public function cookie($name, $value, $expire = 0, $domain = '') {
        $expire = empty($value) ? time() - 60 : ($expire === 0 ? 0 : time() + $expire);
        $base = Req::base();
        if (empty($base))
            $base = '/';
        setcookie($name, $value, $expire, $base, $domain);
        return $this;
    }

    /**
     * 响应缓存头
     * @param int $sec 大于当前时间为到期时间，小于0不缓存
     */
    public function cache($sec) {
        if ($sec <= 0) {
            header('Cache-Control:no-cache,private');
        } else {
            $now = time();
            if ($sec > $now) {
                // 过期时间
                $expire = gmdate('D, d M Y H:i:s', $sec) . ' GMT';
                $last = gmdate('D, d M Y H:i:s', $now) . ' GMT';
                header("Expires: $expire");
                header("Last-Modified: $last");
            } else {
                // 缓存时长
                $last = gmdate('D, d M Y H:i:s', $now) . ' GMT';
                header("Cache-Control:max-age=$sec");
                header("Last-Modified: $last");
            }
        }
        return $this;
    }

    /**
     * JS mini
     * @return string
     */
    public function pack2Js() {
        
    }

    /**
     * css 解析
     * 支持 less 与 scss
     */
    public function pack2Css() {

    }

    /**
     * css less 解析
     * 不解析@import
     * @return string
     */
    public function pack2CssLess() {
        \v::loadThirdClass('/lessc.inc.php');
        $less = new \lessc();
        if (!App::debug())
            $less->setFormatter('compressed');
        $this->body = $less->compile($this->body);
    }

    /**
     * css scss 解析
     * 不解析@import
     * @return string
     */
    public function pack2CssScss() {
        \v::loadThirdClass('/scssphp/scss.inc.php');
        $scss = new \Leafo\ScssPhp\Compiler();
        if (!App::debug())
            $scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
        $this->body = $scss->compile($this->body);
    }

}

?>