<?php
/**
 * 初始化
 *
 */

namespace Core\Base;
use Core\Base\Router;
use Core\Base\Dispatcher;

class Initialize {
  static public function initialize() {
    /* 定义绝对路径 */
    define('ROOT_PATH', dirname(dirname(__DIR__)).'/');
    define('APP_PATH', ROOT_PATH.'/App/'); // 定义应用目录
    define('PUBLIC_PATH', ROOT_PATH.'Public/');

    /* 定义相对于网站根目录的路径 */
    define('__PUBLIC__', '/Public/');
    define('__ROOT__', '/');

    // 是不是ajax
    define(
      'IS_AJAX',
      (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
      )?true:false
    );

    /* 设置include_path,如果要找包含的文件，告诉系统在这个目录下查找。
    get_include_path() 取得当前的环境变量，即php.ini里设置的 include_path;
    PATH_SEPARATOR 是个常量，是include的路径分界符合，在window上是;在unix和Linux上是:
    */
    set_include_path(get_include_path().PATH_SEPARATOR."Core/Smarty/Libs");
    set_include_path(get_include_path().PATH_SEPARATOR."Core/Smarty/Libs/sysplugins");
    set_include_path(get_include_path().PATH_SEPARATOR."Core/Smarty/Libs/plugins");

    /* 加载必需的文件 */
    include(APP_PATH.'Config/'.APP_STATUS.'.php'); // app的配置文件
    include(ROOT_PATH.'Core/Functions/common.php'); // 基本函数
    include(ROOT_PATH.'Core/Base/AutoLoader.class.php'); // 自动加载类

    session_start();
    //注册用于自动加载的函数，spl：PHP标准库
    spl_autoload_register('Core\Base\AutoLoader::autoload');

    /* 错误相关，http://yanue.net/post-99.html */
    /* 定义PHP程序执行完成后执行的函数
    register_shutdown_function() 函数可实现当程序执行完成后执行的函数，其功能为可实现程序执行完成的后续操作。程序在运行的时候可能存在执行超时，或强制关闭等情况，但这种情况下默认的提示是非常不友好的，如果使用register_shutdown_function()函数捕获异常，就能提供更加友好的错误展示方式，同时可以实现一些功能的后续操作，如执行完成后的临时数据清理，包括临时文件等
    */
    register_shutdown_function('Core\Base\Error::fatalError');
    /* 设置一个用户定义的错误处理函数
    如果使用了该函数，会完全绕过标准的 PHP 错误处理函数，如果必要，用户定义的错误处理程序必须终止 (die() ) 脚本
    */
    set_error_handler('Core\Base\Error::appError');
    set_exception_handler('Core\Base\Error::appException');// 自定义异常处理

    /* 解析当前网址，根据解析结果加载不同的controller */
    $router = new Router();
    Dispatcher::dispatch($router);
  }
}