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

    protected $_dependence;     //依赖关系数组
    protected $_reflection;     //反射对象数组
    protected $_map;            //存储注册的依赖对象
    protected $_dependenceParam;//依赖的参数
    protected $_singleton;      //单例对象数组

    /**
     * 注册全局依赖关系
     * @param $alias
     * @param $dependence
     * @param $params
     */
    public function set($alias,$dependence,$params=[]) {
        if(empty($dependence)) {
            $this->_map[$alias] = $alias;
        } else {
            $this->_map[$alias] = $dependence;
        }
        if(is_object($params)) { //对象则保存到单例
            $this->_singleton[$alias] = $params;
        } else {
            $this->_dependenceParam[$alias] = $params;
        }
    }

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
                if($param->isOptional()) {  //是否默认参数
                    $this->_dependence[$class][$param->getName()] = $param->getDefaultValue();
                } else {
                    $typeClass = $param->getClass();
                    $this->_dependence[$class][$param->getName()] = \core\Instance::of(($typeClass == null ? null:$typeClass->getName()));
                }
            }
        }

        return [$this->_reflection[$class],$this->_dependence[$class]];
    }

    protected function resolveDependence($dependence,$reflection) {
        foreach($dependence as $key => $d) {
            if($d instanceof \core\Instance) {
                //获取对象
                if(null != ($id = $d->getId())) {
                    $dependence[$key] = $this->get($id);
                } else {
                    if(null != $reflection) {    //非对象类型的依赖，如果没有默认值或者初始值，则抛出异常
                        throw new \Exception('类'.$reflection->getName().'缺少参数'.$id);
                    }
                }
            }
        }
        return $dependence;
    }

    /**
     * @param $class
     * @param array $params
     * @param array $property
     * @return object
     * @throws \Exception
     */
    public function get($class,$params=[],$property=[]) {
        $map = $this->_map;
        if(!isset($map[$class])) {
            return $this->build($class,$params,$property);
        } else {
            if(isset($this->_singleton[$class])) {
                return $this->_singleton[$class];
            } else {
                return $this->build($map[$class],array_merge($this->_dependenceParam[$class],$params),$property);
            }
        }
    }

    /**
     * @param $class
     * @param array $params
     * @param array $property
     * @return object
     * @throws \Exception
     */
    protected function build($class,$params=[],$property=[]) {
        /** @var $reflection \ReflectionClass  */
        list($reflection,$dependence) = $this->getDependence($class);

        foreach($dependence as $k=>$v) {
            if(isset($params[$k])) {
                $dependence[$k] = $params[$k];
            }
        }

        $dependence = $this->resolveDependence($dependence,$reflection);

        if(!$reflection->isInstantiable()) {
            throw new \Exception($reflection->getName().'不可实例化');
        }

        $obj =  $reflection->newInstanceArgs($dependence);

        foreach($property as $k=>$v) {
            $property = $reflection->getProperty($k);
            if(null != $property) {
                if($property->isStatic()) {
                    $property->setValue($v);
                } else {
                    $property->setValue($obj,$v);
                }
            }
        }

        return $obj;
    }
}