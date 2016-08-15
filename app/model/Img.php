<?php
class Img_Model extends AppModel {

    /**
     * 表名
     * @var string
     */
    protected $table = 'img';

    /*
     * 数据库类型
     */
    protected $dbtype = 'mongodb';
    protected $fields = [
        '_id' => [['striptrim']], //id
        'title' => [['striptrim'], ['*']], // 名称
        'type_id'=> [['striptrim'], ['*']],//typeid
        'imgs'=>[['arr'], ['pass']],
        'size'=>[['arr'], ['pass']],
        'width'=>[['arr'], ['pass']],
        'height' =>[['arr'], ['pass']],
        'memo' => [['striptrim'], ['pass']], // 备注
        'status'=>[['intval'], ['pass']],
        'addtime' => [['intval'], ['pass']], //新建时间
        'puttime' => [['intval'], ['pass']], //修改时间
    ];
    public function hookInsertDataReady(&$data) {
        parent::hookInsertDataReady($data);
        if (!isset($data['status']) || !in_array($data['status'], array(0, 1)))
            $data['status'] = 1;
    }
}

?>