<?php

/**
 * 数据库操作（示例）
 * 先 new 再操作
 */

namespace Model;

use \PHF\Model\Medoo;

class UserInfo extends Medoo {

    public function getTableName($id = null) {
        return "asin_userinfo";
    }

    public function getTablePK() {
        return "qq";
    }

}