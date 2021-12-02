<?php

/**
 * API 的示例
 */
namespace Api;

use \PHF\ApiBase;
use PHF\Exception;
use PHF\Request;

class UserInfo extends ApiBase {

    private $domain;
    public function __construct()
    {
        $this->domain = new \Domain\UserAttr();
    }

    /**
     * 获取用户信息
     *
     * @return void
     */
    public function getUserInfo() {
        $qq = Request::post('qq');
        if (!$qq) {
            Exception::throw('QQ号不能为空', 10001);
        }
        $user = $this->domain->getData($qq);
        if (!$user) {
            Exception::throw('用户不存在', 10101);
        }
        return $user;
    }

    public function getUserInfoWithFight() {
        $qq = Request::post('qq');
        if (!$qq) {
            Exception::throw('QQ号不能为空', 10001);
        }
        $user = $this->domain->getUserAttrWithFight($qq);
        if (!$user) {
            Exception::throw('用户不存在', 10101);
        }
        return $user;
    }

}