<?php

namespace Plugin;

use PHF\Config;
use PHF\Exception;
use PHF\Request;
use PHF\Tools;

use function PHF\PI;

class PlatformLogin {

    public function __construct()
    {
        
    }

    public function wx() {
        $code = Request::param("code");
        $appid = PI()->config->get("wechat.appid", "", "app");
        $appsecret = PI()->config->get("wechat.secret_key", "", "app");
        $req = Tools::request_get("https://api.weixin.qq.com/sns/jscode2session", array(
            "appid" => $appid,
            "secret" => $appsecret,
            "js_code" => $code,
            "grant_type" => "authorization_code"
        ));
        $req = json_decode($req, true);
        if (isset($req["errcode"])) Exception::throw($req["errmsg"], $req['errcode']);
        return $req['openid'];
    }

}