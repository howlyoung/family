<?php

/**
 * 基础实现，自动加载类
 * Class base
 */
define('ROOT_DIR','..'.DIRECTORY_SEPARATOR);    //主目录
class base {
    const SUFFIX_PHP = '.php';

//    const BASE_DIR = [
//        'Controller' => (ROOT_DIR.'Controller'.DIRECTORY_SEPARATOR),
//        'Model' => ROOT_DIR.'Model'.DIRECTORY_SEPARATOR,
//        'View' => ROOT_DIR.'View'.DIRECTORY_SEPARATOR,
//    ];

    public static function autoload($className) {
        if(strpos($className,'\\')) {
            //命名空间
            $fileName = ROOT_DIR.$className.self::SUFFIX_PHP;
            if(file_exists($fileName)) {
                include($fileName);
            }
        }
//        foreach(self::BASE_DIR as $dirSuffix => $dir) {
//            if(strpos($className,$dirSuffix) !== false) {
//                if(file_exists($dir.$name)) {
//                    include($dir.$name);
//                    return ;
//                }
//            }
//        }


    }
}
spl_autoload_register(['base','autoload']);