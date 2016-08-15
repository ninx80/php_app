<?php
/**
 +-----------------------------------------------------------
 * 获取文件的绝对路径
 +-----------------------------------------------------------
 * @param   string  $p  文件地址
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function ME_PATH($p)
{
	if(substr($p,0,1)!='/')
		$p = '/'.$p;
	return getcwd().$p;
}

/**
 +-----------------------------------------------------------
 * 获取文件相对项目的路径
 +-----------------------------------------------------------
 * @param   string  $p  文件地址
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function ROOT_PATH($p)
{
	if(substr($p,0,1)!='/')
		$p = '/'.$p;
	return WEB_ROOT.$p;
}

/**
 +-----------------------------------------------------------
 * 获取地图文件所在位置
 +-----------------------------------------------------------
 * @param   string  $p  文件地址
 +-----------------------------------------------------------
 * @return  string
 +-----------------------------------------------------------
 */
function GET_MAP_PATH($p,$root=false)
{
	$result='';
	$result.=$GLOBALS['_MAP_CONFIG']['file_dir'].$p;
	if($root)
		$result=ROOT_PATH($result);

	return $result;
}
//将文件变成树形结构
function dirToTree($dir,$rid,$pid)
{
    static $id;
    $id=$rid;
    $array=array(array('id'=>$id,'pId'=>$pid,'name'=>$dir,'isParent'=>true));
    $pid=$id;
    $dir_res=opendir($dir);
    while (($file=readdir($dir_res))!==false)
    {
        
        if(isset($file) && $file!='.' && $file!='..')
        {
            $id++;
            $file_path=$dir.'/'.$file;
            
            if(is_dir($file_path))
            {
                
                $array=array_merge($array,dirToTree($file_path,$id,$pid));
                
            }
            elseif(is_file($file_path))
            {
                $array=array_merge($array,array(array('id'=>$id,'pId'=>$pid,'name'=>$file_path,'isParent'=>false)));
                
            }
        }
        
    }
    closedir($dir_res);
    return $array;
}