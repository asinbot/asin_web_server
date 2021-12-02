<?php

/**
 * 数据库操作（示例）
 * 先 new 再操作
 */

namespace Model;

use \PHF\Model\Medoo;

class Example extends Medoo {

    protected function getTableName($id = NULL)
    {
        return 'tablename';
    }

    protected function getTablePK()
    {
        return 'id';
    }

}