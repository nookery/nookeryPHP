<?php

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time=0, $msg='') {
  //多行URL地址支持
  $url = str_replace(array("\n", "\r"), '', $url);
  if (empty($msg))
    $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
  if (!headers_sent()) {
    // redirect
    if (0 === $time) {
      header('Location: ' . $url);
    } else {
      header("refresh:{$time};url={$url}");
      echo($msg);
    }
    exit();
  } else {
    $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
    if ($time != 0)
      $str .= $msg;
    exit($str);
  }
}

/**
 * 获取配置参数
 * @param string $name 变量
 * @return mixed
 */
function C($name = null) {
  return $GLOBALS['appConfig'][$name];
}

/**
 * 获取输入参数 支持过滤和默认值
 * 使用方法:
 * <code>
 * I('id',0); 获取id参数 自动判断get或者post
 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
 * I('get.'); 获取$_GET
 * </code>
 * @param string $name 变量的名称 支持指定类型
 * @param mixed $default 不存在的时候默认值
 * @param mixed $filter 参数过滤方法
 * @param mixed $datas 要获取的额外数据源
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {
    if(strpos($name,'.')) { // 指定参数来源
        list($method,$name) =   explode('.',$name,2);
    }else{ // 默认为自动判断
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   $input =& $_GET;break;
        case 'post'    :   $input =& $_POST;break;
        case 'put'     :   parse_str(file_get_contents('php://input'), $input);break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                    parse_str(file_get_contents('php://input'), $input);
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'path'    :
            $input  =   array();
            if(!empty($_SERVER['PATH_INFO'])){
                $depr   =   C('URL_PATHINFO_DEPR');
                $input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
            }
            break;
        case 'request' :   $input =& $_REQUEST;   break;
        case 'session' :   $input =& $_SESSION;   break;
        case 'cookie'  :   $input =& $_COOKIE;    break;
        case 'server'  :   $input =& $_SERVER;    break;
        case 'globals' :   $input =& $GLOBALS;    break;
        case 'data'    :   $input =& $datas;      break;
        default:
            return NULL;
    }
    if(''==$name) { // 获取全部变量
        $data       =   $input;
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 参数过滤
            }
        }
    }elseif(isset($input[$name])) { // 取值操作
        $data       =   $input[$name];
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }elseif(is_int($filters)){
                $filters    =   array($filters);
            }

            foreach($filters as $filter){
                if(function_exists($filter)) {
                    $data   =   is_array($data)?array_map_recursive($filter,$data):$filter($data); // 参数过滤
                }else{
                    $data   =   filter_var($data,is_int($filter)?$filter:filter_id($filter));
                    if(false === $data) {
                        return   isset($default)?$default:NULL;
                    }
                }
            }
        }
    }else{ // 变量默认值
        $data       =    isset($default)?$default:NULL;
    }
    is_array($data) && array_walk_recursive($data,'think_filter');
    return $data;
}

/**
 * 生成URL
 * @param string OR array $u 如:/docs/add，或array('url'=>'/docs/add')
 */
function U($u) {
  if (is_array($u)) { //如果是smarty传过来的（smarty传过来的是数组)
    extract($u);
  } else {
    $url = $u;
  }

  $url = trim($url, '/'); //变成：docs/add
  $info = parse_url($url);
  $pathArray = explode('/', $url);

  //确定controller和action
  if (1 < count($pathArray)) {
    $action = !empty($pathArray)?array_pop($pathArray):'index';
    $controller = !empty($pathArray)?array_pop($pathArray):'index';
  } else {
    $controller = array_pop($pathArray);
    $action = 'index';
  }

  // var_dump($controller);
  // var_dump($action);

  switch (C('urlMode')) {
    case '1':
      return "/index.php?controller={$controller}&action={$action}";
    case '2':
      if ('index'==$action && 'index'!=$controller) {
        return "/{$controller}/";
      } elseif ('index'==$action && 'index'==$controller) {
        return "/";
      } else {
        return "/{$controller}/{$action}";
      }
  }
}

/**
 * 实例化Event
 * @param string $name 资源名称
 * @return Event|false
 */
function A($name) {
  if(class_exists($name, false)){
    exit('12');
    $class = 'App\Event\\'.$name;
    var_dump($class);exit;
    $action = new $class();
    return $action;
  }else {
    $class = 'App\Event\\'.$name;
    $instance = new $class();
    return $instance;
  }
}

