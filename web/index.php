<?php
use core\request\request;

include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'base.php');
include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'main.php');
$config = include('..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once('../vendor/twig/twig/lib/Twig/Autoloader.php');
\Twig_Autoloader::register();

main::getMain($config)->run();

