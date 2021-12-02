<?php
namespace PHF\Cache;

use PHF\Exception;

/**
 * Class PHF\Cache\Redis
 * @package     PHF\Cache\Redis
 * @license     MIT协议
 * @link        https://pohun.net
 * @author      gumo <gumo@pohun.com> 2020-06-26
 */
class Redis implements \PHF\IFS\Cache
{
    protected $redis;

    protected $auth;

    protected $prefix;

    /**
     * @param array  $conf   Redis配置
     * @param string $conf['type']    Redis连接方式 unix,http
     * @param string $conf["socket"]  unix方式连接时，需要配置
     * @param string $conf["host"]    Redis域名
     * @param int    $conf["port"]    Redis端口,默认为6379
     * @param string $conf["prefix"]  Redis key prefix
     * @param string $conf["auth"]    Redis 身份验证
     * @param int    $conf["db"]      Redis库,默认0
     * @param int    $conf["timeout"] 连接超时时间,单位秒,默认300
     */
    public function __construct($conf)
    {
        $this->redis = new \Redis();

        //Connect
        if (isset($conf['type']) && $conf['type'] == 'unix'){
            if (!isset($conf['socket'])){
                Exception::throw('Redis config miss key `socket`',100);
            }
            $this->redis->connect($conf['socket']);
        } else {
            $port = isset($conf['port']) ? intval($conf['port']) : 6379;
            $timeout = isset($conf['timeout']) ? intval($conf['timeout']) : 300;
            $this->redis->connect($conf['host'], $port, $timeout);
        }

        //Validate
        $this->auth = isset($conf['auth']) ? $conf['auth'] : '';
        if ($this->auth != '')  $this->redis->auth($this->auth);

        //Select
        $db_index = isset($conf['db']) ? intval($conf['db']) : 0;
        $this->redis->select($db_index);

        $this->prefix = isset($conf['prefix']) ? $conf['prefix'] : 'PHF:';
    }

    /**
     * 设置key的值,生存时间为expire秒
     * @param string $key 键
     * @param mixed $value 值
     * @param int $expire 有效时间，秒
     */
    public function set($key, $value, $expire = 600) {
        $this->redis->setex($this->formatKey($key), $expire, $this->formatValue($value));
    }

    public function get($key) {
        $value = $this->redis->get($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function delete($key) {
        return $this->redis->delete($this->formatKey($key));
    }

    /**
     * 拉取缓存，拉取后同时删除缓存
     * @return mixed|NULL 缓存不存在时返回NULL
     */
    public function pull($key) {
        $value = $this->get($key);
        $this->delete($key);
        return $value;
    }

    /**
     * 检测是否存在key,若不存在则赋值value
     * @param string $key 键
     * @param mixed $value 值
     * @return bool TRUE in case of success, FALSE in case of failure.
     */
    public function setnx($key, $value) {
        return $this->redis->setnx($this->formatKey($key), $this->formatValue($value));
    }

    public function lPush($key, $value) {
        return $this->redis->lPush($this->formatKey($key), $this->formatValue($value));
    }

    public function rPush($key, $value) {
        return $this->redis->rPush($this->formatKey($key), $this->formatValue($value));
    }

    public function lPop($key) {
        $value = $this->redis->lPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    public function rPop($key) {
        $value = $this->redis->rPop($this->formatKey($key));
        return $value !== FALSE ? $this->unformatValue($value) : NULL;
    }

    protected function formatKey($key) {
        return $this->prefix . $key;
    }

    protected function formatValue($value) {
        return @serialize($value);
    }

    protected function unformatValue($value) {
        return @unserialize($value);
    }

    /**
     * 获取Redis实例，当封装的方法未能满足时，可调用此接口获取Reids实例进行操作
     */
    public function getRedis() {
        return $this->redis;
    }
}
