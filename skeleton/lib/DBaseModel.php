<?php
namespace v;
/*
 * v framework
 * 
 * v框架数据库操作扩展模型对象
 * 
 * @copyright daojon.com
 * @author daojon <daojon@live.com>
 * @version SVN: $Id: DBaseModel.php 8109 2015-11-08 10:16:13Z wangyong $
 */

//namespace v\ext;

//class_exists('v') or die(header('HTTP/1.1 403 Forbidden'));

//use v;
////
/**
 * 数据库模型类
 */
class DBaseModel extends Model {

    /**
     * 按ID缓存的数据
     * @var array
     */
    protected $cacheIDs = [];

    /**
     * 按ID查找数据
     * @param string $id
     * @param array|string $field
     * @return array
     */
    public function findByID($id, $field = null) {
        return $this->where(['_id' => $id])->findOne($field);
    }

    /**
     * 按ID查找数据
     * 如果是多个ID或者数组则返回多条数据的二维数组，否则返回单条数据
     * 注意limit不能超过系统允许的最大值
     * @param string $id
     * @param array|string $field
     * @return array
     */
    public function findByIDs($id, $field = null) {
        $ids = is_string($id) ? explode(',', strtr($id, [' ' => '', ';' => ','])) : $id;
        return $this->where(['_id' => ['$in' => $ids]])->find($field);
    }

    /**
     * 按ID从程序缓存中取得数据字段
     * @param string $id
     * @param string $field
     * @return array|string|int
     */
    public function fieldByID($id, $field = null) {
        if (!isset($this->cacheIDs[$id])) {
            $this->cacheIDs[$id] = $this->findByID($id);
        }
        return empty($field) ? $this->cacheIDs[$id] : array_keys_value($field, $this->cacheIDs[$id]);
    }

    /**
     * 按ID更新数据
     * @param string|array $id
     * @param array $data
     * @return boolean
     */
    public function updateByID($id, &$data = null) {
        $rs = $this->where(['_id' => $id])->options(['multiple' => false])->update($data);
        return $rs;
    }

    /**
     * 按ID更新数据
     * @param string|array $id
     * @param array $data
     * @return boolean
     */
    public function updateByIDs($id, &$data = null) {
        $ids = is_string($id) ? explode(',', strtr($id, [' ' => '', ';' => ','])) : $id;
        $rs = count($ids) == 1 ? $this->updateByID($ids[0], $data) : $this->where(['_id' => ['$in' => $ids]])->update($data);
        return $rs;
    }

    /**
     * 按ID删除数据
     * @param string|array $id
     * @return boolean
     */
    public function removeByID($id) {
        return $this->where(['_id' => $id])->options(['justOne' => true])->remove();
    }

    /**
     * 按ID删除数据
     * @param string|array $id
     * @return boolean
     */
    public function removeByIDs($id) {
        $ids = is_string($id) ? explode(',', strtr($id, [' ' => '', ';' => ','])) : $id;
        if (count($ids) == 1) {
            // 单条数据
            return $this->where(['_id' => $id])->remove();
        }
        // 多条数据
        return $this->where(['_id' => ['$in' => $ids]])->remove();
    }

    /**
     * 取得where条件中的ID
     * @param array $where 查询条件
     * @return array
     */
    public function queryIDs($where = null) {
        $criteria = is_null($where) ? $this->where : $where;
        // 从查询条件中取ID
        if (isset($criteria['_id']) && count($criteria) == 1) {
            if (is_string($criteria['_id'])) {
                return [$criteria['_id']];
            } elseif (count($criteria['_id']) == 1 && isset($criteria['_id']['$in'])) {
                return $criteria['_id']['$in'];
            }
        }
        if (!is_null($where) && $this->lastOP === self::FIND) {
            // 从查找到数据中取ID
            return array_key_values('_id', $this->fdata);
        }
        // 查询数据库取ID
        $items = $this->where($criteria, ['_id'])->find();
        return array_key_values('_id', $items);
    }

    /**
     * 设置错误消息
     * @param string $field
     * @param string $msg
     * @return \v\ext\DBaseModel
     */
//    public function errMessage($field, $msg = null) {
//        v\Err::set($field, $msg);
//        $this->canop = false;
//        return $this;
//    }

}
