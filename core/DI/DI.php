<?php
namespace core\DI;
/**
 * 依赖容器
 * Created by PhpStorm.
 * User: yanghao
 * Date: 2019/3/25
 * Time: 16:21
 */
class DI
{

    protected $_dependence; //依赖关系数组
    protected $_reflection; //反射对象数组

    /**
     * @param $class
     * @return array
     */
    protected function getDependence($class) {
        if(isset($this->_reflection[$class])) {
            return $this->_dependence[$class];
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        $this->_reflection[$class] = $reflection;
        $this->_dependence[$class] = [];

        if($constructor) {
            foreach($constructor->getParameters() as $key => $param) {
                if($typeClass = $param->getClass()) {
                    $this->_dependence[$class][$key] = [$param->getName() =>$typeClass->getName()];
                } else  {
                    $this->_dependence[$class][$key] = $param->getName();
                }
            }
        }

        return [$this->_reflection[$class],$this->_dependence[$class]];
    }

    protected function resolveDependence($dependence) {
        foreach($dependence as $key => $d) {
            if(is_array($d)) {
                //获取对象
                $dependence[$key] = $this->build(current($d));
            }
        }
        return $dependence;
    }

    public function get($class,$params=[],$property=[]) {
        return [];
    }

    /**
     * 创建对象
     *
     * @param $class
     * @param $params
     * @param $property
     */
    public function build($class,$params=[],$property=[]) {
        list($reflection,$dependence) = $this->getDependence($class);

        foreach($dependence as $k=>$v) {
            if(isset($params[$v])) {
                $dependence[$k] = $params[$v];
            }
        }
        $dependence = $this->resolveDependence($dependence);


        return $reflection->newInstanceArgs($dependence);

    }
}