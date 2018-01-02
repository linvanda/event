<?php

namespace member_eventlib\infrastructure\utils;

/**
 * 对象操作助手
 *
 * Class ObjectUtilTrait
 * @package member_eventlib\infrastructure\utils
 */
trait ObjectUtilTrait
{
    /**
     * 内部使用的便捷的根据入参设置对应属性
     * 注意：只有当入参名和属性名完全一样是才能使用此方法
     * 调用方式：$this->setProperties(__METHOD__, func_get_args());
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    protected function setProperties($method, $args)
    {
        $class = new \ReflectionClass(get_class($this));
        $m = $class->getMethod(strpos($method, '::') !== false ? explode('::', $method)[1] : $method);
        $params = $m->getParameters();

        $i = 0;
        foreach ($params as $param) {
            if (property_exists($this, $param->name) && isset($args[$i])) {
                $this->{$param->name} = $args[$i++];
            }

            if (! isset($args[$i])) {
                break;
            }
        }
    }

    protected function collectData($upperCaseToUnderline = true, $includeNull = false)
    {
        $data = [];

        foreach ($this->getPropertiesByDoc('@data') as $property) {
            $val = $this->{$property};

            if ($val === null && ! $includeNull) {
                continue;
            }

            $key = $upperCaseToUnderline ? $this->upperCaseToUnderline($property) : $property;
            $data[$key] = $val;
        }

        return $data;
    }

    /**
     * 根据@required注解获取必填属性列表
     */
    protected function requiredProperties()
    {
        return $this->getPropertiesByDoc('@required');
    }

    /**
     * 根据注解获取属性列表
     *
     * @param string $doc
     * @return array
     */
    private function getPropertiesByDoc($annotation)
    {
        $data = [];
        $class = new \ReflectionClass(get_class($this));
        $properties = $class->getProperties();

        foreach ($properties as $property) {
            $doc = $property->getDocComment();

            if (preg_match("/$annotation/", $doc)) {
                $data[] = $property->name;
            }
        }

        return $data;
    }

    private function upperCaseToUnderline($word)
    {
        $str = '';
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            $asc = ord($char);
            if ($asc > 64 && $asc < 91) {
                $str .= '_' . strtolower($char);
            } else {
                $str .= $char;
            }
        }

        return $str;
    }
}
