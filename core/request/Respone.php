<?php
/**
* 处理请求后的返回
 */

namespace core\request;


class Respone
{
    protected $type;    //应答类型
    protected $content; //应答内容

    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * 将应答加入到输出缓冲
     */
    public function addContentToBuffer() {
        switch(gettype($this->content)) {
            case 'array':
                print_r($this->content);
                break;
            case 'object':
                var_dump($this->content);
                break;
            case 'string':
            case 'integer':
            case 'double':
            case 'boolean':
                echo $this->content;
                break;
            default:
                //不支持类型，无输出
                break;
        }
    }
}