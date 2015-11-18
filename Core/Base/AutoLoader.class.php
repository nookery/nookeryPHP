<?php
/**
 * 自动加载类
 * 实例化一个类时，发现没有定义，会从这里加载
 */

namespace Core\Base;

class AutoLoader {
  static function autoload($object) {
    //object可能是BaseController，也可能是带命名空间的：App\Controller\BaseController
    if (strstr($object, '\\')) {
      //如果是App\Controller\BaseController，转换成App/Controller/BaseController
      $object = str_replace('\\', '/', $object);
    }
    // var_dump($object);
    //寻找要加载的文件
    if (file_exists(ROOT_PATH."{$object}.class.php")) {
      require_once(ROOT_PATH."{$object}.class.php");
    } else {
      //一般到这一步的都是smarty的相关文件
      //如果上面的路径都找不到，会去set_include_path中设置的找
      if (strstr($object, 'smarty') || strstr($object, 'Smarty')) {
        $object = strtolower($object);
        require_once("$object.php");
      } else {
        require_once("$object.php");
      }
    }
  }
}