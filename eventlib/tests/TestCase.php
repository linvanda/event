<?php

namespace member_eventlib\tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * 创建桩件
     * @param $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function createMock($className)
    {
        return $this->getMockBuilder($className)->getMock();
    }

    /**
     * 执行私有方法，并返回执行结果
     * @param $instance
     * @param $method
     * @param array $args
     * @return mixed
     */
    public function methodInvoke($instance, $methodName, array $args = [])
    {
        $class = get_class($instance);
        $method = new \ReflectionMethod($class, $methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($instance, $args);
    }

    /**
     * 获取私有属性
     * @param $instance
     * @param $propertyName
     * @return mixed
     */
    public function propertyGet($instance, $propertyName)
    {
        $class = get_class($instance);
        $property = new \ReflectionProperty($class, $propertyName);
        $property->setAccessible(true);
        return $property->getValue($instance);
    }

    /**
     * 设置私有属性
     * @param $instance
     * @param $propertyName
     * @param $value
     */
    public function propertySet($instance, $propertyName, $value)
    {
        $class = get_class($instance);
        $property = new \ReflectionProperty($class, $propertyName);
        $property->setAccessible(true);
        $property->setValue($instance, $value);
    }
}
