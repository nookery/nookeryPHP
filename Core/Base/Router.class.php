<?php
/**
 * 解析URL
 * 目前支持的URL格式：
 * 2: index.php?home/index/id/1
 * 1: index.php?controller=home&action=index&id=1
 */

namespace Core\Base;

class Router {
  private $route;
  private $controller;
  private $action;
  private $params;

  public function __construct() {
    $appConfig = $GLOBALS['appConfig'];
    switch ($appConfig['urlMode']) {
      case '2':
        $routeParts = explode("/", $_GET['s']);
        $controller =
          (isset($routeParts[1]) && !empty($routeParts[1]))?
          ucfirst($routeParts[1])
          :"Index";
        $controllerClass = "App\\Controllers\\{$controller}Controller";
        $action = (isset($routeParts[2]) && !empty($routeParts[2]))?$routeParts[2]:"index";
        array_shift($routeParts); //删除$routeParts数组中的第一个元素
        array_shift($routeParts);//删除$routeParts数组中的第一个元素
        $params = $routeParts;
        break;
      case '1': //index.php?controller=index&action=index
        $get = $_GET;
        $route = $_GET;
        $controller =
          isset($get['controller'])?
          ucfirst($get['controller']):
          'Index';
        $controllerClass = "App\\Controllers\\{$controller}Controller";
        $action =
          isset($get['action'])
          ?$get['action']
          :'index';
        unset($get['controller'], $get['action']);
        $params = $get;
        break;
    }

    $this->controller = $controllerClass;
    $this->action = $action;
    $this->params = $params;
    define('ACTION_NAME', $action);
  }

  /**
   * 返回当前的action
   * @return string
   */
  public function getAction() {
    return $this->action;
  }

  /**
   * 返回当前的controller
   * @return string
   */
  public function getController()  {
    return ucfirst($this->controller);
  }

  /**
   * 返回参数
   * @return string
   */
  public function getParams()  {
    return $this->params;
  }
}