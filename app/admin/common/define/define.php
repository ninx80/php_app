<?php

/**
*定义debug
*/
define('DEBUG_MODE', 0);

/**
*定义系统环境:     1 -> win32      0 -> linux 
*/
define('PLATFORM', 1);

/**
*定义COM组件及组件类
*/
define('DOMAIN_COM_CLASS', 'uc_com.IUC_Interface');
define('OMNI_COM_CLASS', 'omnicom.nvrServer');

/**
*定义domain数据常量
*/
define('WEB_SERVER_PORT', 81);
define('DOMAIN_IP', '172.16.80.65');
/** CMS 端口 */
//define('DOMAIN_PORT', 9020);
//define('DOMAIN_NMSPORT', 15501);
/** VMS 端口 */
define('DOMAIN_PORT', 9050);
define('DOMAIN_NMSPORT', 15502);

/**
*定义omni相关
*/
define('OMNI_DATA_ROOT', "C:/omni/");   /** 定义待读取的文件路径 */ 
$GLOBALS['TIME_OUT'] = 5500;    /** 定义omni超时，单位 ms ，必须大于100 ,应当大于5000 */


/**
 * 
******************定义DID常量********************
*/

/**
 * 
*DID 148 常量
*/
$GLOBALS['DID_148_module']=array
(
    0   =>  'NVR 500',
    1   =>  'NVR 1000',
    2   =>  'NVR 2000',
    3   =>  'NVR 3000',
    11  =>  'VMS',
    21  =>  'SMR2000',
    22  =>  'SMR 5000',
    23  =>  'SMR 8000',
);

/**
 * 
*DID 185 常量
*/
$GLOBALS['DID_185_DiskType']=array
(
    0   =>  'UNKNOWN',
    1   =>  'SCSI',
    2   =>  'ATAPI',
    3   =>  'ATA',
    4   =>  '_1934',
    5   =>  'SSA',
    6   =>  'FIBRE',
    7   =>  'USB',
    8   =>  'RAID',
    9   =>  'ISCSI',
    10  =>  'SAS',
    11  =>  'SATA',
    12  =>  'SAS',
);
$GLOBALS['DID_185_RAIDLevel']=array     /** *********** 尚有疑问******** */
(
    2   =>  'RAID0',
    3   =>  'RAID1',
    5   =>  'RAID3',
    7   =>  'RAID5',
    9   =>  'RAID6',
    11  =>  'JBOD',
    12  =>  'NON-RAID',
);
$GLOBALS['DID_185_DiskDataType']=array
(
    0   =>  'NO',
    1   =>  'OS',
    2   =>  'APP',
    3   =>  'OS,APP',
    4   =>  'LOG',
    5   =>  'OS,LOG',
    6   =>  'APP,LOG',
    7   =>  'OS,APP,LOG',
    8   =>  'VIDEO',
    9   =>  'OS,VIDEO',
    10  =>  'APP,VIDEO',
    11  =>  'OS,APP,VIDEO',
    12  =>  'LOG,VIDEO',
    13  =>  'OS,LOG,VIDEO',
    14  =>  'APP,Log,VIDEO',
    15  =>  'OS,LOG,APP,VIDEO',
);




?>