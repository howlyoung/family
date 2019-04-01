<?php
/**

 */

namespace core\request;


class Respone
{
    protected $type;
    protected $content;

    public function setContent($content) {
        $this->content = $content;
    }


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

                break;
        }
    }
}