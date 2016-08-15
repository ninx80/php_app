<?php

/*
 * 
 * 图片上传控制器对象
 */

class Upload_Controller extends Controller {

    public function resPost() {
        if (!empty($_FILES)) {
            if (!empty($_POST['from'])) {
                if ($_POST['from'] == 'admin') {
                    $this->upImgByAdmin();
                }
            }
            v\Res::apion('上传失败')->end(400);
        } else {
            v\Res::apion('没有上传文件')->end(400);
        }
    }

    public function upImgByAdmin() {
        $file = $_FILES['file'];
        $dir = APP_PREV_ROOT . 'twodogs/tdimgs' ;
        if (!is_dir($dir)) {
            $mkrs = mkdir($dir, 0755, true);
            if (!$mkrs) {
                v\Res::apion('创建上传目录失败')->end(400);
            }
        }
        if ($file['size'] > 1024000) {
            v\Res::apion('上传图片大小不能超过1M')->end(400);
        }
        if (!in_array($file['type'], array('image/jpeg', 'image/gif')) || !preg_match('/(jpg|jpeg|gif)$/i', $file['name'], $temp)) {
            v\Res::apion('只能上传jpg,gif图片')->end(400);
        }
        list($w, $h) = getimagesize($file['tmp_name']);
        $file_name = 'td'.uniqid() . '.' . $temp[1];
        $all_file_name=$dir . '/' . $file_name;
        if (move_uploaded_file($file['tmp_name'], $all_file_name)) {
            v\Res::apion(array('path' => '/twodogs/tdimgs/' . $file_name, 'filename' => $file_name, 'width' => $w, 'height' => $h))->end(200);
        }
        
    }

    public function _adUnit() {
        $file = $_FILES['file'];
        $ader_id = $_POST['ader_id'];
        $dir = APP_ROOT . '/static/adimg/' . $ader_id;
        if (!is_dir($dir)) {
            $mkrs = mkdir($dir, 0755, true);
            if (!$mkrs) {
                v\Res::apion('创建上传目录失败')->end(400);
            }
        }
        if ($file['size'] > 1024000) {
            v\Res::apion('上传图片大小不能超过1M')->end(400);
        }
        if ($_POST['type'] == 2) {
            if (!in_array($file['type'], array('image/jpeg', 'image/gif')) || !preg_match('/(jpg|jpeg|gif)$/i', $file['name'], $temp)) {
                v\Res::apion('只能上传jpg,gif图片')->end(400);
            }
            list($w, $h) = getimagesize($file['tmp_name']);
            $adsize = v\App::config('ad_size.php');
            $adsize=$adsize['pc'];
            $valid = false;
            foreach ($adsize as $size) {
                $s = explode('x', $size);
                if ($w == $s[0] && $h == $s[1]) {
                    $valid = true;
                    break;
                }
            }

            if (!$valid) {
                v\Res::apion('请上传支持的图片尺寸')->end(400);
            }
        } elseif ($_POST['type'] == 4) {
            if (!in_array($file['type'], array('image/x-icon')) || !preg_match('/(ico)$/i', $file['name'], $temp)) {
                v\Res::apion('只能上传ico图片')->end(400);
            }
            list($w, $h) = getimagesize($file['tmp_name']);
        } elseif ($_POST['type'] == 3) {
            if (!in_array($file['type'], array('application/octet-stream', 'application/x-shockwave-flash')) || !preg_match('/(swf)$/i', $file['name'], $temp)) {
                v\Res::apion('只能上传SWF文件')->end(400);
            }
            $w = $h = 0;
        }
        $file_name = uniqid() . '.' . $temp[1];
        if (move_uploaded_file($file['tmp_name'], $dir . '/' . $file_name)) {
            v\Res::html(json_encode(array('path' => 'adimg/' . $ader_id . '/' . $file_name, 'filename' => $file_name, 'width' => $w, 'height' => $h)))->end(200);
        }
    }

    function _softFile() {
        $file = $_FILES['file'];
        $dir = APP_ROOT . '/static/clientsoft/'.md5($_GET['distributor_id']).'/';
        $file_path = $dir . $file['name'];
        $fileinfo = pathinfo($file_path);
        $filename = $fileinfo['filename'] . '_' . date('Ymd-His'); //date('Y-m-d H:i:s')
        $file_path_new = $fileinfo['dirname'] . '/' . $filename . '.' . $fileinfo['extension'];
        $file_path_new = iconv("UTF-8", "GBK", $file_path_new);
        if (!is_dir($dir)) {
            $mkrs = mkdir($dir, 0755, true);
            if (!$mkrs) {
                v\Res::apion('上传失败')->end(400);
            }
        }
        if (file_exists($file_path_new)) {
            v\Res::apion('文件名重复,上传失败!')->end(400);
        }
        if ($file['size'] > 10240000) {
            v\Res::apion('上传文件大小不能超过10M')->end(400);
        }
        if (!preg_match('/(exe|dll)$/i', $file['name'], $temp)) {
            v\Res::apion('只能上传exe,dll文件')->end(400);
        }
        if (move_uploaded_file($file['tmp_name'], $file_path_new)) {
            return ['name' => $file['name'], 'path' => $file_path_new];
        } else {
            v\Res::apion('文件上传失败!')->end(400);
        }
    }

}
