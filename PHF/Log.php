<?php

namespace PHF;

class Log
{
    const kLogFatal = "Fatal";
    const kLogError = "Error";
    const kLogWarn  = "Warn ";
    const kLogInfo  = "Info ";
    const kLogDebug = "Debug";
    const kLogSQL   = "SQL  ";

    private static $logPath = null;
    private static $instance;

    public static function one($path = null) {
        if (static::$instance == NULL) {
            static::$instance = new Log();
            static::$instance->onConstruct($path);
        }

        return static::$instance;
    }

    public function onConstruct($path)
    {
        self::$logPath = $path;
    }

    /**
     * 写入Log文件
     * @param $filePath
     * @param $text
     */
    private static function write($filePath,$text) {
        if (self::$logPath) {
            $pathArr = [DIR_ROOT.self::$logPath, date('Ymd')];
        } else {
            $pathArr = [DIR_ROOT.'log', date('Ymd')];
        }
        $folder = implode(DIRECTORY_SEPARATOR, $pathArr);

        $name = date('H');

        $ext = strrchr($filePath, '.');

//        $folder = implode(DIRECTORY_SEPARATOR, [DIR_ROOT, $path]);
        if (!is_dir($folder)) {
            mkdir($folder);
        }
        $filePath = $folder . DIRECTORY_SEPARATOR . $name . $ext;

        $text = Tools::getTime()."|".str_replace(array("\r","\n"),"",$text)."\n\n";

        file_put_contents($filePath, $text , FILE_APPEND | LOCK_EX);
    }

    public static function Error ($text) {
        $filePath = 'error.log';
        self::write($filePath,$text);
    }

    public static function Debug ($text) {
        $filePath = 'debug.log';
        self::write($filePath,$text);
    }

    public static function Info ($text) {
        $filePath = 'info.log';
        self::write($filePath,$text);
    }

    public static function Sql ($text) {
        $filePath = 'sql.log';
        self::write($filePath,$text);
    }

}
