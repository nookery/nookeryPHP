<?php

//目前的URL格式
//2: index.php?home/index/id/1
//1: index.php?controller=home&action=index&id=1

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

define('APP_STATUS', 'develop');
include("./Core/Base/Initialize.class.php");
Core\Base\Initialize::initialize();
