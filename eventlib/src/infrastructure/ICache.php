<?php

namespace member_eventlib\infrastructure;

/**
 * 缓存接口
 * Interface ICache
 * @package member_eventlib\infrastructure
 */
interface ICache
{
    /**
     * @param string $name
     * @return object
     */
    public function get($name);

    /**
     * @param string $name
     * @param object $value
     * @param int $timeout
     * @return void
     */
    public function set($name, $value = null, $timeout = 0);

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name);

    /**
     * @param string $name
     * @return void
     */
    public function delete($name);
}
