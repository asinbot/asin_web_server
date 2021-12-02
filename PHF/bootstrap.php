<?php
namespace PHF;
/**
 * 框架入口文件
 */

define('DIR_ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
define('PHF_VERSION', '1.0.0');
date_default_timezone_set('Asia/Shanghai');

require_once DIR_ROOT . 'PHF' . DIRECTORY_SEPARATOR .  'Loader.php';

spl_autoload_register('\PHF\Loader::autoload');

//require_once DIR_ROOT.'Plugin'.DIRECTORY_SEPARATOR.'init.php';

if( $_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    exit;
}

if ($_SERVER && PI()->config->get('canCrossDomain')) {
    $_origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
    header("Access-Control-Allow-Origin:". $_origin);
    header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE");//允许跨域的请求方式
    // header("Access-Control-Max-Age:". "3600");//预检请求的间隔时间
    header("Access-Control-Allow-Headers: enctype, Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With,userId,token,Access-Control-Allow-Headers");//允许跨域请求携带的请求头
    header("Access-Control-Allow-Credentials: true");
    // addheader("Access-Control-Allow-Headers”,”enctype”);
}

/**
 * 获取PI
 * Plugin::one()
 * @return \PHF\Plugin
 */
function PI():Plugin
{
    return \PHF\Plugin::one();
}
