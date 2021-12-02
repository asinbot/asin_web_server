<?php

/**
 * 请求类
 */

namespace PHF;

class Request {

    public static function post($key, $value = null) {
        return isset($_POST[$key]) ? $_POST[$key] : ($value ? $value : null);
    }

    public static function file($key, $value = null) {
        return isset($_FILES[$key]) ? $_FILES[$key] : ($value ? $value : null);
    }

    public static function get($key, $value = null) {
        return isset($_GET[$key]) ? $_GET[$key] : ($value ? $value : null);
    }

    public static function input($key = null, $value = null) {
        $var = file_get_contents('php://input');
        if (Tools::isJson($var)){
            $var = json_decode($var,true);
        }elseif (Tools::isXml($var)) {
            $var =Tools::xmlToArray($var);
        }
        if ($key === null){
            return $var;
        }
        return isset($var[$key]) ? $var[$key] : ($value ? $value : null);
    }

    public static function cookie($key, $value = null) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : ($value ? $value : null);
    }

    /**
     * 获取不同来源的参数
     * @param $key
     * @param null $value
     * @return mixed|null
     */
    public static function param($key, $value = null) {
        if(isset($_GET[$key])) {
            $var = &$_GET;
        } elseif (isset($_POST[$key])) {
            $var = &$_POST;
        } else {
            $var = file_get_contents('php://input');
            if (Tools::isJson($var)){
                $var = json_decode($var,true);
            }elseif (Tools::isXml($var)) {
                $var =Tools::xmlToArray($var);
            }
        }
        return isset($var[$key]) ? $var[$key] : ($value ? $value : null);
    }

    /**
     * getJsonValue，从json串中获取数据
     * @param   string  $param  json名
     * @param   string  $key    参数名
     * @param   mixed   $value  默认返回值
     * 
     * @return  mixed   返回值 
     */
    public static function jv($param, $key, $value = null) {
        $var = self::param($param);
        if (!$var) return $value;
        if (!is_array($var)) $var = json_decode($var, true);
        $keys = explode(PI()->config->get('paramKeySeparator', '.'), $key);
        $rs = null;
        foreach ($keys as $val) {
            if (!isset($var[$val])) {
                $rs = null;
                break;
            }
            $var = $rs = $var[$val];
        }
        return $rs !== null ? $rs : $value;
    }

}