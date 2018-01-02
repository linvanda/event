<?php

namespace member_eventlib\infrastructure;

/**
 * 缓存：测试用，使用数组模拟
 * Class TestCache
 * @package member_eventlib\infrastructure
 */
class TestCache implements ICache
{
    private $cache = [];

    public function get($name)
    {
        return $this->cache[$name];
    }

    public function set($name, $value = null, $timeout = 0)
    {
        $this->cache[$name] = $value;
    }

    public function exists($name)
    {
        return array_key_exists($name, $this->cache);
    }

    public function delete($name)
    {
        unset($this->cache[$name]);
    }
}
