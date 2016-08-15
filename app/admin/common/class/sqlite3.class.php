<?php

class sqlite3
{
    private $dbFile = 'D:\sqliteadmin\phpTestSqlite3\test20150331.s3db';
    private $query1 = 'SELECT id, name, age, address FROM workers';
    private $db = NULL;


    /** 检测 DB 文件是否已经存在 */
    /** 返回值：成功 true，失败 false */
    public function DBIsExist($sql)
    {
        
    }
    
    /** 根据请求获取数据库数据 */
    /** 返回值：成功 array()，失败 NULL */
    public function getDBData($dbFile, $sql)
    {
        $row = NULL;
        $db = new SQLite3($dbFile); 
        $result = $this->db->query($sql);
        $i = 0; 
        while($res = $result->fetchArray(SQLITE3_ASSOC))
		{ 
            if(!isset($res['id'])) continue; 
            $row[$i]['id'] = $res['id']; 
            $row[$i]['name'] = $res['name']; 
            $row[$i]['age'] = $res['age']; 
            $row[$i]['address'] = $res['address']; 
            $i++; 
        }
        
    }

}
?>