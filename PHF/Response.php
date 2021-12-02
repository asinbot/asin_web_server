<?php

/**
 * 响应类
 */

namespace PHF;

class Response {
    
    public static function return($data = null, $msg = '', $code = 200) {
        self::json($data, $msg, $code);
    }

    public static function json($data = null, $msg = '', $code = 200) {
        echo json_encode(array(
            'errCode' => $code,
            'errMsg' => $msg,
            'data' => $data
        ));
        exit(0);
    }

    public static function xml($data = null, $msg = 'OK', $code='SUCCESS')
    {
        $xml = "<xml>";
//        if ($data){
//            $xml .= '<return_data>';
//            foreach ($data as $key => $val) {
//                if (is_array($val)) {
//                    $xml .= "<" . $key . ">" . self::xml($val) . "</" . $key . ">";
//                } else {
//                    $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
//                }
//            }
//            $xml .= '</return_data>';
//        }
        if ($msg){
            $xml .= "<return_msg><![CDATA[{$msg}]]></return_msg>";
        }
        if ($code){
            $xml .= "<return_code><![CDATA[{$code}]]></return_code>";
        }
        $xml .= "</xml>";
        echo $xml;
        exit(0);
    }
}