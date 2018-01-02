<?php

namespace member_eventclient;

class ServiceProvider extends \member_eventlib\ServiceProvider
{
    /**
     * 事件源仓储
     *
     * @return \member_eventclient\repository\IEventSourceRepository
     */
    public static function sourceRepository()
    {
        return static::get('source_repository');
    }

    /**
     * 事件发布器
     *
     * @return \member_eventclient\publisher\EventPublisher
     */
    public static function eventPublisher()
    {
        return static::get('event_publisher');
    }

    /**
     * 重写服务配置加载方法：增加本地配置
     *
     * @return array
     */
    protected static function &loadMapping()
    {
        static $mapping;

        if (! $mapping) {
            $commonMapping = parent::loadMapping();
            $localMapping = require_once 'config/ServiceMapping.php';

            foreach ($commonMapping as $scene => &$config) {
                if ($localMapping[$scene]) {
                    $config = array_replace($config, $localMapping[$scene]);
                }
            }

            $mapping = $commonMapping;
        }

        return $mapping;
    }
}
