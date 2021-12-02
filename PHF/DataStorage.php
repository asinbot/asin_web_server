<?php
namespace PHF;

class DataStorage
{
    public static $storagePath = DIR_ROOT;

    public static function SetData(string $filePath, $data, bool $pending = false){
        if(!file_exists(dirname(static::$storagePath.$filePath))){
            if(!mkdir(dirname(static::$storagePath.$filePath), 0777, true))
            throw new \Exception('Failed to create data dir');
        }
        return @file_put_contents(static::$storagePath.$filePath, $data, $pending?(FILE_APPEND | LOCK_EX):LOCK_EX);
    }

    public static function GetData(string $filePath){
        return @file_get_contents(static::$storagePath.$filePath);
    }

    public static function DelData(string $filePath){
        return @\unlink(static::$storagePath.$filePath);
    }
}
