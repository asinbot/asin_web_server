<?php


namespace PHF;


class PHF
{
    /**
     * 启动 API 进程
     */
    public static function start() {

        //获取 mod 和 action
        $mod = Request::param('mod', Request::param('s'));
        $action = Request::param('action');

        $_modArr = explode('/', $mod);
        if (empty($action)) {
            // 如果未在参数中设置action，则从mod中分离action
            if (count($_modArr) < 2) Exception::throw('请求方法不存在', -403);
            $action = array_pop($_modArr);
        }
        $mod = implode('\\', $_modArr);
        $className = '\Api\\'.$mod;
        if (empty($mod)) Exception::throw('没有请求模组', -402);
        if (!class_exists($className)) Exception::throw('请求模组不存在', -402);
        $class = new $className();
        if (!method_exists($class, $action)) Exception::throw('请求方法不存在', -403);
        Response::return($class->$action());
    }
}