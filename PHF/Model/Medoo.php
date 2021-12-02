<?php

/**
 * 基于Medoo DB类驱动的Model
 * DB类说明文档 https://medoo.lvtao.net/1.2/doc.php
 */


namespace PHF\Model;


abstract class Medoo implements \PHF\IFS\Model {

    private $db;
    private $columns = []; //数据表字段信息

    final function __construct() {
        $this->db = \PHF\PI()->medoo;
    }

    /** ---------------- CURD基本操作 ---------------- **/

    /**
     * 指定表名，可根据主键实现分表
     * @param mixed $id
     * @return string
     */
    abstract protected function getTableName($id = NULL);

    /**
     * 指定表主键名
     * @return string
     */
    abstract protected function getTablePK();

    /**
     * 获取当前数据库实例
     * @return \PHF\Database\Medoo
     */
    public function getDB(){
        return $this->db;
    }

    /**
     * 插入新纪录
     * 这里看起来有点奇怪，但如果我们需要进行分表存储，这里的参考主键是需要的
     *
     * @param array $data 待插入的数据，可以包括ext_data字段
     * @param mixed $id 分表参考主键
     * @return false|\PDOStatement 新插入纪录的主键值
     */
    public function insert($data, $id = NULL){
        $table = $this->getTableName($id);
        return $this->getDB()->insert($table,$data);
    }

    /**
     * 根据条件更新纪录
     *
     * @param mixed $where 更新条件
     * @param array $data 待更新的数据
     * @param mixed $id 分表参考主键
     * @return false|\PDOStatement
     */
    public function update($where, $data,$id = NULL){
        if (!is_array($where)) {
            $where = array($this->getTablePK() => $where);
        }
        $table = $this->getTableName($id);
        return $this->getDB()->update($table,$data,$where);
    }

    /**
     * 根据条件删除纪录
     *
     * @param mixed $where 删除条件
     * @param mixed $id 分表参考主键
     * @return int the number of rows.
     */
    public function delete($where,$id = NULL){
        if (!is_array($where)) {
            $where = array($this->getTablePK() => $where);
        }
        $table = $this->getTableName($id);
        $data = $this->getDB()->delete($table,$where);
        return $data->rowCount();
    }

    /**
     * 根据条件批量获取数据
     * @param array $where 查询条件
     * @param array|string $columns 需要获取的列
     * @param string|array $order
     * ```
     *  $order = [
     *   // Order by column with descending sorting
     *   "profile_id" => "DESC",
     *
     *   // Order by column with ascending sorting
     *   "date" => "ASC"
     *   ];
     *
     *  // Single condition
     *  $order = "user_id";
     *
     * ```
     * @param integer $limits
     * @param integer $limitn
     * @param array|null $join 联表查询
     * @param mixed $id
     * @return array|bool
     */
    public function fetch($where= [],$columns = '*',$order='',$limits=0,$limitn=0,$join = null,$id = NULL){
        $table = $this->getTableName($id);

        if (!empty($order)){
            $where['ORDER'] = $order;
        }
        if ($limitn !== 0){
            $where['LIMIT'] = [$limits,$limitn];
        }

        if($join === null){
            $res = $this->getDB()->select($table,  $columns === '*'? $this->getColumns() : $columns, $where);
        }else{
            $res = $this->getDB()->select($table,  $join, $columns, $where);
        }
        return $res;
    }

    /**
     * 是否存在符合条件的数据
     * @param mixed $where 查询条件
     * @param mixed $id 分表参考主键
     * @return bool
     */
    public function has($where,$id = NULL){
        if (!is_array($where)) {
            $where = array($this->getTablePK() => $where);
        }
        $table = $this->getTableName($id);
        return $this->getDB()->has($table, $where);
    }

    /**
     * 随机获取数据
     * @param null $where
     * @param array|string $columns
     * @param null $join
     * @param mixed $id
     * @return array|bool
     */
    public function rand($where = NULL,$columns = '*',$join = null,$id = NULL){
        $table = $this->getTableName($id);
        if($join === null){
            $res = $this->getDB()->rand($table,  $columns === '*'? $this->getColumns() : $columns, $where);
        }else{
            $res = $this->getDB()->rand($table,  $join, $columns, $where);
        }
        return $res;
    }

    /**
     * 从表中返回一行数据
     * @param mixed $where
     * @param array|string $columns
     * @param null $join
     * @param mixed $id
     * @return array|mixed
     */
    public function get($where = NULL, $columns = "*",$join = NULL,$id = NULL){
        if ($columns === '*') $columns = $this->getColumns();
        if (!is_array($where)) {
            $where = array($this->getTablePK() => $where);
        }
        $table = $this->getTableName($id);
        if ($join===NULL) return $this->getDB()->get($table, $columns, $where);
        return $this->getDB()->get($table,$join, $columns, $where);
    }

    /**
     * 查询数据数量
     *
     * @param array $where
     * @return void
     */
    public function count(array $where = array()) {
        if (is_array($where) && count($where) > 0) {
            return $this->getDB()->count($this->getTableName(), array($this->getTablePK()), $where);
        } else {
            return $this->getDB()->count($this->getTableName(), array($this->getTablePK()));
        }
    }

    /**
     * 获取数据表字段信息
     * @param mixed $id
     * @param bool $need_type
     * @return array
     */
    protected function getColumns($id = NULL,$need_type = false){
        if ($this->columns) return $this->columns;
        $table = $this->getDB()->tableQuote($this->getTableName($id));
        $result = $this->getDB()->query('SHOW COLUMNS FROM '.$table)->fetchAll();
        $this->columns = [];
        if ($need_type === true){
            foreach ($result as $col){
                $this->columns[$col['Field']] = $col['Type'];
            }
        }else{
            foreach ($result as $col){
                $this->columns[] = $col['Field'];
            }
        }

        return $this->columns;
    }

    /**
     * 根据表字段过滤数据
     *
     * @param array $datas 需要过滤的数据
     * @return array
     */
    public function filterData($datas) {
        $fields = $this->getColumns();
        $data = array();
        foreach ($datas as $key => $value) {
            if (in_array($key, $fields)) $data[$key] = $value;
        }
        return $data;
    }

}
