<?php

/**
 * API 的示例
 */
namespace Api;

use \PHF\ApiBase;
use PHF\Request;

class UserInfo extends ApiBase {

    private $domain;
    public function __construct()
    {
        $this->domain = new \Domain\UserInfo();
    }

    /**
     * 获取用户信息
     *
     * @return void
     */
    public function getUserInfo() {
        echo "-----------------getUserInfo\n";
        $qq = Request::post('qq');
        $user = $this->domain->getData($qq);
        return $user;
    }

}