<?php
/**
 * 控制器
 */

namespace Core\Base;

class Controller {
  /**
  * 魔术方法 有不存在的操作的时候执行
  * @access public
  * @param string $method 方法名
  * @param array $args 参数
  * @return mixed
  */
  public function __call($method, $args) {
    if (method_exists($this, '_empty')) {
      $this->_empty($method, $args); // 如果定义了_empty操作 则调用
    } else {
      throw new \Exception("找不到action，请检查{$method}是否存在");
    }
  }

  public function __construct() {
    $this->smarty = new \Smarty;
    $this->smarty->setTemplateDir(APP_PATH.'/Views'); //设置模板目录
    $this->smarty->setConfigDir("Core/Smarty/Configs");
    $this->smarty->setCompileDir('runtime/views_c');
    $this->smarty->setCacheDir('runtime/cache');
    $this->smarty->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
    $this->smarty->registerPlugin("function", "U", "U");
    $this->smarty->force_compile = true;
    $this->smarty->debugging = false;
    $this->smarty->caching = false;
    $this->smarty->cache_lifetime = 120;
  }

  /**
   * 为smarty变量赋值
   * @param  string $key
   * @param  string $value
   * @return void
   */
  public function assign($key, $value) {
    $this->smarty->assign($key, $value);
  }

  /**
   * Action跳转(URL重定向） 支持指定模块和延时跳转
   * @access protected
   * @param string $url 跳转的URL表达式
   * @param array $params 其它URL参数
   * @param integer $delay 延时跳转的时间 单位为秒
   * @param string $msg 跳转提示信息
   * @return void
   */
  protected function redirect($url,$params=array(),$delay=0,$msg='') {
    redirect(U($url),$delay,$msg);
  }

  /**
   * 设置参数
   * @param array $params 参数数组，如：{ [0]=> string(2) "id" [1]=> string(1) "1" }
   */
  public function setParams($params) {
    $this->params = $params;
  }

  public function display($view) {
    $this->smarty->display($view);
  }
}