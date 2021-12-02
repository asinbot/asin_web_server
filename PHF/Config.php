<?php

/**
 * 配置类
 */

namespace PHF;

class Config {

    private static $paramKeySeparator = '.';
    /**
     * @var string $path 配置文件的目录位置
     */
    private $path = '';

    /**
     * @var array $map 配置文件的映射表，避免重复加载
     */
    private $map = array();

    public function __construct($configPath) {
        $this->path = $configPath;
    }

    /**
     * 获取配置
     * 首次获取时会进行初始化
     * @param string $key
     * @param mixed $def 默认返回值
     * @param null $file 请求的配置文件，默认为global，无需携带.php
     * @return mixed    返回值
     */
    public function get(string $key, $def = null, $file = null){
        $file = $file ?? 'global';
        $keyArr = explode(self::$paramKeySeparator, $key);
        if (!isset($this->map[$file])) {
            $this->loadConfig($file);
        }
        $rs = NULL;
        $preRs = $this->map[$file];
        foreach ($keyArr as $subKey) {
            if (!isset($preRs[$subKey])) {
                $rs = NULL;
                break;
            }
            $rs = $preRs[$subKey];
            $preRs = $rs;
        }

        return $rs !== NULL ? $rs : $def;
    }

    /**
     * 加载配置文件
     * 加载保存配置信息数组的config.php文件，若文件不存在，则将$map置为空数组
     *
     * @param string|null $fileName 配置文件路径
     * @return array 配置文件对应的内容
     */
    private function loadConfig(?string $fileName) {
        $configFile = $this->path . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (!file_exists($configFile)) {
            Exception::throw('配置文件不存在');
        }

        $config = @include($configFile);
        $this->map[$fileName] = $config;
    }

}
