<?php
/**
 * 处理错误
 */

namespace Core\Base;

class Error {
  /**
   * 自定义错误处理
   * @access public
   * @param int $errno 错误类型
   * @param string $errstr 错误信息
   * @param string $errfile 错误文件
   * @param int $errline 错误行数
   * @return void
   */
  static public function appError($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
      case E_NOTICE:
        //notice级别的错误，不输出
        break;
      case E_WARNING:
        //warning级别的错误，不输出
        break;
      case E_ERROR:
      case E_PARSE:
      case E_CORE_ERROR:
      case E_COMPILE_ERROR:
      case E_USER_ERROR:
        ob_end_clean();
        $errorStr = "$errstr ".$errfile." 第 $errline 行.";
        self::halt($errorStr);
        break;
      default:
        $errorStr = "
          错误类型：[$errno]<br/>
          错误信息：$errstr<br/>
          错误文件：$errfile<br/>
          错误行数：第 $errline 行<br/>";
        self::halt($errorStr);
        break;
    }
  }

  /**
   * 自定义异常处理
   * @access public
   * @param mixed $e 异常对象
   */
  static function appException($e) {
    $error = array();
    $error['message'] = $e->getMessage();
    $trace = $e->getTrace();
    if('E'==$trace[0]['function']) {
      $error['file']  =   $trace[0]['file'];
      $error['line']  =   $trace[0]['line'];
    }else{
      $error['file']  =   $e->getFile();
      $error['line']  =   $e->getLine();
    }
    $error['trace']     =   $e->getTraceAsString();
    // 发送404信息
    header('HTTP/1.1 404 Not Found');
    header('Status:404 Not Found');
    include 'Core/Views/exception.php';
    exit;
  }

  /**
   * 程序执行完后执行的操作，有些级别的错误self::appError是捕捉不到的
   * @return void
   */
  static public function fatalError() {
    //获取最后一条错误
    if ($e = error_get_last()) {
        switch($e['type']){
          case E_ERROR:
          case E_PARSE:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:
            //致命的错误，要停止运行，清除以前的所有输出
            ob_end_clean();
            self::halt($e);
            break;
        }
    }
  }

  /**
   * 错误输出
   * @param mixed $error 错误
   * @return void
   */
  static public function halt($error) {
    $e = array();
    if (!is_array($error)) {
      $trace = debug_backtrace();
      $e['message'] = $error;
      $e['file'] = $trace[0]['file'];
      $e['line'] = $trace[0]['line'];
      ob_start();
      debug_print_backtrace();
      $e['trace'] = ob_get_clean();
    } else {
      $e = $error;
    }

    include dirname(__DIR__).'/Views/exception.php';
    exit;
  }
}