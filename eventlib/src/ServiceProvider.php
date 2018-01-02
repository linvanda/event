<?php

namespace member_eventlib;

use Pimple\Container;

/**
 * 一个简单的服务提供者，用于实现依赖注入
 * 此处使用第三方库Pimple实现
 *
 * Class ServiceProvider
 * @package member_eventlib\infrastructure
 */
class ServiceProvider
{
    protected static $container;

    /**
     * 注册服务
     *
     * @param string $name 服务名称，如cache，queue
     * @param string|object $service 服务类名或实例
     * @return void
     */
    public static function register($name, $service)
    {
        $scene = defined('RUN_SCENE') ? RUN_SCENE : 'normal';

        static::mapping($scene, $name, $service);

        if (static::container()->offsetExists($name)) {
            static::container()->offsetUnset($name);
        }
    }

    /**
     * 缓存
     *
     * @return \member_eventlib\infrastructure\ICache
     */
    public static function cache()
    {
        return static::get('cache');
    }

    /**
     * 消息队列
     *
     * @return \member_eventlib\infrastructure\IMessageQueue
     */
    public static function queue()
    {
        return static::get('queue');
    }

    /**
     * 日志
     *
     * @return \member_eventlib\infrastructure\log\ILog
     */
    public static function logger()
    {
        return static::get('log');
    }

    /**
     * 垃圾事件收集器
     *
     * @return \member_eventlib\event\maintain\IGarbageCollection
     */
    public static function garbageCollection()
    {
        return static::get('garbage_collection');
    }

    public static function get($name, $params = [])
    {
        if (! static::container()->offsetExists($name)) {
            static::simpleCreate($name, $params);
        }

        return static::container()[$name];
    }

    /**
     * 此处用的是单例模式
     * @param string $alias
     * @return void
     */
    protected static function simpleCreate($alias, $params = [])
    {
        $realService = static::realService($alias);

        if (!$realService) {
            throw new \InvalidArgumentException("$alias has not been set in ServiceProvider");
        }

        static::container()[$alias] = function () use ($alias, $realService, $params) {
            if (is_string($realService)) {
                return (new \ReflectionClass($realService))->newInstanceArgs($params);
            } else {
                return $realService;
            }
        };
    }

    /**
     * 注入器
     * @return \Pimple\Container
     */
    protected static function container()
    {
        if (! static::$container) {
            static::$container = new Container();
        }

        return static::$container;
    }

    /**
     * @param string $alias
     * @return string|object
     */
    protected static function realService($alias)
    {
        $scene = defined('RUN_SCENE') ? RUN_SCENE : 'normal';

        if ($scene == 'unittest' && ! static::mapping()[$scene][$alias]) {
            $scene = 'normal';
        }

        if (! static::mapping()[$scene] || ! static::mapping()[$scene][$alias]) {
            return '';
        }

        return static::mapping()[$scene][$alias];
    }

    /**
     * @param string $scene
     * @param string $name
     * @param string $value
     * @return array|void
     */
    protected static function mapping($scene = '', $name = '', $value = '')
    {
        $mapping = &static::loadMapping();

        if ($scene && $name && $value) {
            if (! $mapping[$scene]) {
                $mapping[$scene] = [];
            }

            $mapping[$scene][$name] = $value;
        } else {
            return $mapping;
        }
    }

    /**
     * 重写该方法时注意：必须返回引用，否则外界调用register无效
     *
     * @return array
     */
    protected static function &loadMapping()
    {
        static $mapping;

        if (! $mapping) {
            $mapping = require_once 'config/ServiceMapping.php';
        }

        return $mapping;
    }
}
