<?php
/**
 * 处理错误
 */

namespace Core\Base;

class Error {
  /**
   * 自定义错误处理
   * E_ERROR、E_PARSE、E_CORE_ERROR、E_CORE_WARNING、 E_COMPILE_ERROR、E_COMPILE_WARNING是不会被捕捉到的
   * http://www.runoob.com/php/php-error.html
   * @access public
   * @param int    $errno   错误类型
   * @param string $errstr  错误信息
   * @param string $errfile 错误文件
   * @param int    $errline 错误行数
   * @param array  $error_context 包含了当错误发生时在用的每个变量以及它们的值
   * @return void
   */
  static public function appError($errno, $errstr, $errfile, $errline, $error_context) {
    switch ($errno) {
      case E_NOTICE: // 8，notice级别的错误
      case E_WARNING: // 2，非致命的 run-time 错误
        break;
      case E_USER_ERROR: // 致命的用户生成的错误
      default:
        ob_end_clean();
        $errorArray['type'] = '['.E_USER_ERROR.']E_USER_ERROR';
        $errorArray['message'] = $errstr;
        $errorArray['file'] = $errfile;
        $errorArray['line'] = $errline;
        ob_start();
        debug_print_backtrace();
        $errorArray['trace'] = ob_get_clean();
        include dirname(__DIR__).'/Views/appError.php';
        exit;
        break;
    }
  }

  /**
   * 自定义异常处理
   * @access public
   * @param mixed $e 异常对象
   */
  static function appException($e) {
    $errorArray = array(
      'message' => $e->getMessage(),
      'file' => $e->getFile(),
      'line' => $e->getLine(),
      'trace' => $e->getTraceAsString(),
    );

    header('HTTP/1.1 404 Not Found'); // 发送404信息
    header('Status:404 Not Found');
    include dirname(__DIR__).'/Views/exception.php';
  }

  /**
   * 致命错误
   * 程序执行完后执行的操作，有些级别的错误self::appError是捕捉不到的
   * @return void
   */
  static public function fatalError() {
    // error_get_last() 函数获取最后发生的错误。
    // 该函数以数组的形式返回最后发生的错误。
    // 返回的数组包含 4 个键和值：
    // [type] - 错误类型
    // [message] - 错误消息
    // [file] - 发生错误所在的文件
    // [line] - 发生错误所在的行
    if ($errorArray = error_get_last()) {
      switch($errorArray['type']){
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
          ob_end_clean(); // 致命的错误，要停止运行，清除以前的所有输出
          ob_start(); // 开启缓冲区
          debug_print_backtrace(); // 输出调试信息
          $errorArray['trace'] = ob_get_clean(); // 获取刚输出到缓冲区的调试信息
          include dirname(__DIR__).'/Views/fatalError.php';
          break;
      }
    }
  }
}