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
    protected $_map;        //存储注册的依赖对象

    /**
     * 注册全局依赖关系
     * @param $alias
     * @param $dependence
     */
    public function set($alias,$dependence) {
        if(is_string($dependence)) {

        } elseif(is_array($dependence)) {

        } elseif(is_object($dependence)) {
            $this->_map[$alias] = $dependence;
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
                    if(isset($this->_map[$key])) {  //
                        $dependence[$key] = $this->_map[$key];
                    } else {
                        $dependence[$key] = $this->build($id);
                    }
                } else {
                    if(null != $reflection) {    //非对象类型的依赖，如果没有默认值或者初始值，则抛出异常
                        throw new \Exception('类'.$reflection->getName().'缺少参数'.$id);
                    }
                }
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
            if(isset($params[$k])) {
                $dependence[$k] = $params[$k];
            }
        }
        $dependence = $this->resolveDependence($dependence,$reflection);


        return $reflection->newInstanceArgs($dependence);

    }
}