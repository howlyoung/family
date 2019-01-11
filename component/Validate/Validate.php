<?php
namespace component\Validate;
/**
 */
class Validate
{

    public function verifyPassword($pwd) {

    }

    public function verifySign() {

    }

    public static function getValidate($type) {
        $suffix = 'Validate';
        $className = __NAMESPACE__.DIRECTORY_SEPARATOR.$type.$suffix;
        if(class_exists($className)) {
            return new $className;
        } else {
            return null;
        }
    }
}