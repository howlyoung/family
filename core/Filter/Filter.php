<?php

namespace core\Filter;
/**
* 过滤字符串
 */
class Filter
{
    public static function filterRequest($str) {
        if(!is_string($str)) {
            return $str;
        } else {
            $origin = $str;
            return htmlspecialchars($origin);
        }
    }
}