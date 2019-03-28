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

    /**
     * 注册全局依赖关系
     * @param $alias
     * @param $dependence
     */
    public function set($alias,$dependence) {
        if(is_string($dependence)) { //约定$alias要生成的类，可用于依赖接口或者抽象类的情况
            $this->_map[$alias] = $dependence;
        } elseif(is_array($dependence)) {   //设置需要依赖的类需要的参数
            if(count($dependence)>1&&isset($dependence['class'])) {
                $this->_map[$alias] = $dependence['class'];
                unset($dependence['class']);
                $arrValue = current($dependence);
            } else {
                $this->_map[$alias] = $alias;
                $arrValue = $dependence;
            }
            $this->_dependenceParam[$alias] = $arrValue;
        } elseif(empty($dependence)) {
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

        //此处等于依赖了_map，本方法应该专注处理依赖，对class别名的处理应该交给上层的方法
        $className = isset($this->_map[$class])?$this->_map[$class]:$class; //查看是否设置了依赖，如果设置了则取_map中存储的类名作为反射类的参数
        $reflection = new \ReflectionClass($className);

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
                    $dependence[$key] = $this->build($id);
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
     * @param $class
     * @param array $params
     * @param array $property
     * @return object
     * @throws \Exception
     */
    public function build($class,$params=[],$property=[]) {
        /** @var $reflection \ReflectionClass  */
        list($reflection,$dependence) = $this->getDependence($class);

        foreach($dependence as $k=>$v) {
            if(isset($params[$k])) {
                $dependence[$k] = $params[$k];
            } elseif(isset($this->_dependenceParam[$class][$k])) {
                $dependence[$k] = $this->_dependenceParam[$class][$k];
            }
        }
        $dependence = $this->resolveDependence($dependence,$reflection);

        if(!$reflection->isInstantiable()) {
            throw new \Exception('不可实例化');
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