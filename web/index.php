<?php

define('MAIN_PATH','..'.DIRECTORY_SEPARATOR);   //主目录
define('VIEW_PATH',MAIN_PATH.'View'.DIRECTORY_SEPARATOR);       //视图文件夹

include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'base.php');
include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'main.php');
$config = include('..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

main::getMain($config)->run();

