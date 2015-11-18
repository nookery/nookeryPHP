<?php
/**
 * 根据路由决定加载哪个controller
 */

namespace Core\Base;

class Dispatcher {
  /**
   * 实例化controller
   * @param  class $router 路由
   * @return void
   */
  public static function dispatch($router) {
    //得到的controller类似: App\Controller\BaseController，而不是: Base
    $controller = $router->getController();
    $action = $router->getAction();
    $params = $router->getParams();
    $controllerfile = str_replace('\\', '/', $controller).'.class.php';
    if (file_exists($controllerfile)) {
      require_once($controllerfile);
      $app = new $controller();
      $app->setParams($params);
      $app->$action();
    }else{
      throw new \Exception("找不到controller，请检查{$controllerfile}是否存在");
    }
  }
}

