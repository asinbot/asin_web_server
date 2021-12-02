<?php


namespace PHF\IFS;


interface Model {
//暂时不写
//    /**
//     * 根据主键读取纪录
//     *
//     * @param mixed $where 查询字段
//     * @param string/array $fields 需要获取的表字段，可以为字符串(如：name,from)或数组(如：array("name", "from"))
//     * @return array 数据库表纪录
//     */
//    public function get($where, $fields = "*");
//
//    /**
//     * 插入新纪录
//     * 这里看起来有点奇怪，但如果我们需要进行分表存储，这里的参考主键是需要的
//     *
//     * @param array $data 待插入的数据，可以包括ext_data字段
//     * @param mixed $id 分表参考主键
//     * @return mixed 新插入纪录的主键值
//     */
//    public function insert($data, $id = NULL);
//
//    /**
//     * 根据主键更新纪录
//     *
//     * @param mixed $where 查询字段
//     * @param array $data 待更新的数据，可以包括ext_data字段
//     * @return bool
//     */
//    public function update($where, $data);
//
//    /**
//     * 根据主键删除纪录
//     *
//     * @param mixed $where 查询字段
//     */
//    public function delete($where);
//
//    //select 必须
//
//    //has 是否存在
//    public function has($where);
//
//    //rand 随机获取数据
//    public function rand($columns,$where = NULL);
}
