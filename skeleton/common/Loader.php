<?php
// 加载系统文件
$_loadfile = [
    SYS_ROOT.'lib/parents/com.php',
    SYS_ROOT.'lib/parents/Fac.php',
    SYS_ROOT.'lib/parents/svc.php',
    SYS_ROOT.'lib/Cvt.php',
    SYS_ROOT.'lib/Chk.php',
    SYS_ROOT.'lib/Res.php',
    SYS_ROOT.'lib/Err.php',
    SYS_ROOT.'lib/Model.php',
    SYS_ROOT.'lib/DBaseModel.php',
    SYS_ROOT.'lib/DBase.php',
    SYS_ROOT.'lib/ext/MongoDBase.php',
    SYS_ROOT.'lib/ext/SqlDBase.php',
    SYS_ROOT.'lib/BaseController.php',
    SYS_ROOT.'lib/App.php',
    SYS_ROOT.'lib/JShrink.php',
    SYS_ROOT.'lib/Play.php',
];
foreach($_loadfile as $file)
	!file_exists($file) || include($file);
// 加载APP中自定义文件夹和文件
foreach(CONF('APP_LOAD_FILE') as $file){
    if(is_file($file=APP_ROOT.$file))
        include($file);
}
foreach(CONF('APP_LOAD_PATH') as $path){
    $path=APP_ROOT.$path;
    if(is_dir($path)){
        chdir($path);
        $files = glob('*.php');
        foreach($files as $file)
            include($path.'/'.$file);
    }
}