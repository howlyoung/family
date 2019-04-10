<?php
namespace base;
/**
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/4/3
 * Time: 10:22
 */
class View
{

    /**
     * @param $fileName
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function render($fileName,$params=[]) {
        if(!file_exists($fileName)) {
            throw new \Exception('没有找到页面!'.$fileName);
        }
        extract($params);
        ob_start();
        ob_implicit_flush(false);
        include($fileName);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}