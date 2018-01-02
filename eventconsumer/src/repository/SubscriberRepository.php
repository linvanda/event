<?php

namespace member_eventconsumer\repository;

use member_eventconsumer\ServiceProvider;
use member_eventconsumer\subscriber\Subscriber;

/**
 * 订阅者仓储
 *
 * Class SubscriberRepository
 * @package member_eventconsumer\repository
 */
abstract class SubscriberRepository
{
    /**
     * 根据订阅者id返回订阅者对象
     *
     * @param \string $id
     * @return \member_eventconsumer\subscriber\Subscriber
     */
    public function find($id)
    {
        $subscribers = $this->allSubscriber();

        return $subscribers ? $subscribers[$id] : null;
    }

    /**
     * 根据租户以及事件分组和分类获取相关订阅者列表
     *
     * @param $tenant string 发生事件的租户
     * @param string $eventGroup 事件分组
     * @param string $eventName 事件
     * @return array
     */
    public function all($tenant, $eventGroup, $eventName, $source)
    {
        $subscribers = $this->allSubscriber();
        $theRights = [];

        foreach ($subscribers as $subscriber) {
            if (! $subscriber instanceof Subscriber) {
                continue;
            }

            if ($this->hasSubscribed($tenant, $eventGroup, $eventName, $source, $subscriber)) {
                $theRights[] = $subscriber;
            }
        }

        return $theRights;
    }

    /**
     * 检查该订阅者是否订阅了该事件
     *
     * 订阅者的事件配置格式：
     * [
     *      'tenant' => [
     *                  'eventgroup' => [
     *                          'event' => ['eventname1', 'eventname2'],
     *                          'in_source' => ['sourceid1', 'sourceid2'],
     *                          'ex_source' => ['sourceid1', 'sourceid2'],
     *                 ]
     *      ]
     * ]
     *
     * @param string $tenant
     * @param string $eventGroup
     * @param string $eventName
     * @param string $source
     * @param Subscriber $subscriber
     * @return bool
     */
    private function hasSubscribed($tenant, $eventGroup, $eventName, $source, Subscriber $subscriber)
    {
        $subEvents = $subscriber->subscribedEvents();

        if (! $subEvents) {
            return false;
        }

        if (! array_key_exists($tenant, $subEvents)) {
            if (! array_key_exists('*', $subEvents)) {
                return false;
            } else {
                $events = [$subEvents['*']];
            }
        } else {
            $events = [$subEvents[$tenant]];

            if (array_key_exists('*', $subEvents)) {
                $events[] = $subEvents['*'];
            }
        }

        if (! $events) {
            return false;
        }

        //该租户对应的事件配置信息
        foreach ($events as $tenantEvents) {
            if (! ($groupInfo = $tenantEvents[$eventGroup])) {
                continue;
            }

            //事件源过滤
            if (is_array($groupInfo['in_source']) && $groupInfo['in_source']) {
                if (! $this->inSource($source, $groupInfo['in_source'])) {
                    continue;
                }
            } elseif (
                is_array($groupInfo['ex_source']) &&
                $groupInfo['ex_source'] &&
                $this->inSource($source, $groupInfo['ex_source'])
            ) {
                continue;
            }

            if (! ($realEvents = $groupInfo['event'])) {
                continue;
            }

            if (in_array('*', $realEvents) || in_array($eventName, $realEvents)) {
                return true;
            }
        }

        return false;
    }

    private function inSource($source, $arr)
    {
        foreach ($arr as $item) {
            if (
                strpos($item, '*') === false && $item == $source
                || strpos($item, '*') === strlen($item) - 1 && strpos($source, rtrim($item, '*')) === 0
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * 所有订阅者列表
     *
     * @return array
     */
    abstract protected function allSubscriber();
}
