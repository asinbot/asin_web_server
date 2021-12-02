<?php

namespace PHF;

class ApiBase {

    protected $_needJWT = false;

    public function __construct() {
        //校验参数
        // 经过考虑，规定继承类不应使用 __init 初始化，而直接使用 parent::__construct() 继承
        // 理由为 类 $this 变量在 __init 内赋值，在其他方法内无法获取自动获取到值，代码提示问题
        //  $this->__init();
         $this->userCheck();
    }

    protected function userCheck(){
        if ($this->_needJWT) {
            // $status = JWT::checkJWT(getgpc('jwt', 'PARAM'));
            $status = JWT::checkJWT(Request::param('jwt'));
            if ($status === -1) {
                // return $this->setResults(401, '登录态无效');
                Exception::throw('登录态无效', -401);
            } elseif ($status === 2)  {
                Exception::throw('登录态过期', -401);
                // return $this->setResults(402, '登录态过期');
            }
        }
    }

    //  protected function __init() {

    //  }

//    public function setResults($code = 200, $msg = '', $data = null) {
//        echo json_encode(array(
//            'errCode' => $code,
//            'errMsg' => $msg,
//            'data' => $data
//        ));
//        exit(0);
//    }

}
