<?php

namespace PHF;

use ArrayAccess;
use Closure;
use PHF\Cache\Redis;
use PHF\Database\Medoo;
use Plugin\DesEncrypt;
use Plugin\Wechat;

/**
 *  Plugin容器
 *
 * - 调用的方式有：set/get函数、魔法方法setX/getX、类变量$fPlugin->X、数组$fPlugin['X]
 * - 初始化的途径：直接赋值、类名、匿名函数
 *
 * <br>使用示例：<br>
 * ```
 *       $Plugin = new Plugin();
 *
 *       // 用的方式有：set/get函数  魔法方法setX/getX、类属性$Plugin->X、数组$Plugin['X']
 *       $Plugin->key = 'value';
 *       $Plugin['key'] = 'value';
 *       $Plugin->set('key', 'value');
 *       $Plugin->setKey('value');
 *
 *       echo $Plugin->key;
 *       echo $Plugin['key'];
 *       echo $Plugin->get('key');
 *       echo $Plugin->getKey();
 *
 *       // 初始化的途径：直接赋值、类名(会回调onInitialize函数)、匿名函数
 *       $Plugin->simpleKey = array('value');
 *       $Plugin->classKey = 'DependenceInjection';
 *       $Plugin->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
 * ```
 *
 * @property Request $request    请求
 * @property Response $response   响应
 * @property Redis $redis      redis缓存
 * @property Config $config     配置
 * @property Log $log     日记
 * @property Loader $loader     自动加载器
 * @property Medoo $medoo     数据库
 * @property Wechat $wechat 微信SDK
 * @property DesEncrypt $encrypt 加密解密
 * @package     PhalApi\DependenceInjection
 * @link        http://docs.phalconphp.com/en/latest/reference/di.html 实现统一的资源设置、获取与管理，支持延时加载
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-01
 */
class Plugin implements ArrayAccess
{

    /**
     * @var Plugin $instance 单例
     */
    protected static $instance = NULL;

    /**
     * @var array $hitTimes 服务命中的次数
     */
    protected $hitTimes = array();

    /**
     * @var array 注册的服务池
     */
    protected $data = array();

    public function __construct()
    {

    }

    /**
     * 获取Plugin单体实例
     *
     * - 1、将进行service级的构造与初始化
     * - 2、也可以通过new创建，但不能实现service的共享
     */
    public static function one()
    {
        if (static::$instance == NULL) {
            static::$instance = new Plugin();
            static::$instance->onConstruct();
        }

        return static::$instance;
    }

    /**
     * service级的构造函数
     *
     * - 1、可实现一些自定义业务的操作，如内置默认service
     * - 2、首次创建时将会调用
     */
    public function onConstruct()
    {
        $this->config = new Config(DIR_ROOT.'config');
        $this->medoo = new \PHF\Database\Medoo($this->config->get('db'));
        $this->request = '\\PHF\\Request';
        $this->response = '\\PHF\\Response';
        $this->log = \PHF\Log::one();
    }

    public function onInitialize()
    {
    }

    /** ------------------ 魔法方法 ------------------ **/

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'set') {
            $key = lcfirst(substr($name, 3));
            return $this->set($key, isset($arguments[0]) ? $arguments[0] : NULL);
        } else if (substr($name, 0, 3) == 'get') {
            $key = lcfirst(substr($name, 3));
            return $this->get($key, isset($arguments[0]) ? $arguments[0] : NULL);
        }
        Exception::throw('未知调用：Plugin::'.$name,100);
    }

    /**
     * 统一setter
     *
     * - 1、设置保存service的构造原型，延时创建
     *
     * @param string $key service注册名称，要求唯一，区分大小写
     * @parms mixed $value service的值，可以是具体的值或实例、类名、匿名函数、数组配置
     * @return Plugin
     */
    public function set($key, $value)
    {
        $this->hitTimes[$key] = 0;

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * 统一getter
     *
     * - 1、获取指定service的值，并根据其原型分不同情况创建
     * - 2、首次创建时，如果service级的构造函数可调用，则调用
     * - 3、每次获取时，如果非共享且service级的初始化函数可调用，则调用
     *
     * @param string $key service注册名称，要求唯一，区分大小写
     * @param mixed $default service不存在时的默认值
     * @return mixed 没有此服务时返回NULL
     */
    public function get($key, $default = NULL)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = $default;
        }

        // 内联操作，减少函数调用，提升性能
        if (!isset($this->hitTimes[$key])) {
            $this->hitTimes[$key] = 0;
        }
        $this->hitTimes[$key]++;

        if ($this->hitTimes[$key] == 1) {
            $this->data[$key] = $this->initService($this->data[$key]);
        }

        return $this->data[$key];
    }

    /** ------------------ 内部方法 ------------------ **/

    protected function initService($config)
    {
        $rs = NULL;

        if ($config instanceOf Closure) {
            $rs = $config();
        } elseif (is_string($config) && class_exists($config)) {
            $rs = new $config();
            if (is_callable(array($rs, 'onInitialize'))) {
                call_user_func(array($rs, 'onInitialize'));
            }
        } else {
            $rs = $config;
        }

        return $rs;
    }

    public function __get($name)
    {
        return $this->get($name, NULL);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /** ------------------ ArrayAccess（数组式访问）接口 ------------------ **/

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset, NULL);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }
}

