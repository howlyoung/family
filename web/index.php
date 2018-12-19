<?php
use core\request\request;
include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'base.php');
include('..'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'main.php');
try {
    main::getMain()->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}
