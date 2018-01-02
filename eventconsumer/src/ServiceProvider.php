<?php

namespace member_eventconsumer;

class ServiceProvider extends \member_eventlib\ServiceProvider
{
    /**
     * 分发封包发布器
     * @return \member_eventconsumer\publisher\DispatchPublisher
     */
    public static function dispatchPublisher()
    {
        return static::get('dispatch_publisher');
    }

    /**
     * 分发器
     *
     * @return \member_eventconsumer\dispatcher\IDispatcher
     */
    public static function dispatcher()
    {
        return static::get('dispatcher');
    }

    /**
     * 订阅者仓储
     * @return \member_eventconsumer\repository\SubscriberRepository
     */
    public static function subscriberRepository()
    {
        return static::get('subscriber_repository');
    }

    /**
     * 事件仓储
     *
     * @param string $tenant 租户码
     * @return \member_eventconsumer\repository\EventRepository
     */
    public static function eventRepository($tenant)
    {
        return static::get('event_repository', [$tenant]);
    }

    /**
     * 事件消费日志
     *
     * @return \member_eventconsumer\repository\EventLogRepository
     */
    public static function eventLog($tenant)
    {
        return static::get('eventlog_repository', [$tenant]);
    }

    /**
     * 订阅者代理
     *
     * @return \member_eventconsumer\subscriber\SubscriberProxy
     */
    public static function subscriberProxy()
    {
        return static::get('subscriber_proxy');
    }

    /**
     * 订阅者健康审查者
     *
     * @return \member_eventconsumer\subscriber\maintain\IHealthInspector
     */
    public static function healthInspector()
    {
        return static::get('health_inspector');
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
