<?php

namespace member_eventconsumer\repository;

use member_eventconsumer\factory\SubscriberFactory;
use member_eventconsumer\ServiceProvider;
use yii\db\Query;

/**
 * 针对公司Yii框架的仓储实现
 *
 * Class YiiSubscriberRepository
 * @package member_eventconsumer\repository
 */
class YiiSubscriberRepository extends SubscriberRepository
{
    private $cacheKey = 'member_event_subscribers';

    protected function allSubscriber()
    {
        $cache = ServiceProvider::cache();

        if (
            (! defined('MEMBER_EVENT_DEBUG') || ! MEMBER_EVENT_DEBUG)
            && $cache->exists($this->cacheKey)
            && ($cacheSubs = $cache->get($this->cacheKey))
        ) {
            return $cacheSubs;
        } else {
            $subscribers = [];

            foreach ($this->allFromDb() as $config) {
                if ($config['tenants']) {
                    $config['tenants'] = json_decode($config['tenants'], true);
                }

                if ($config['events']) {
                    $config['events'] = json_decode($config['events'], true);
                }

                if ($config['cert_info']) {
                    $config['cert_info'] = json_decode($config['cert_info'], true);
                }

                if ($config['client_extra_info']) {
                    $config['client_extra_info'] = json_decode($config['client_extra_info'], true);
                }

                if ($config['middlewares']) {
                    $config['middlewares'] = json_decode($config['middlewares'], true);
                }

                if ($config['customize_data']) {
                    $config['customize_data'] = json_decode($config['customize_data'], true);
                }

                $subscribers[$config['id']] = SubscriberFactory::createFromConfData($config);
            }

            $cache->set($this->cacheKey, $subscribers, 7200);

            return $subscribers;
        }
    }

    /**
     * @return array
     */
    private function allFromDb()
    {
        return (new Query())
            ->select('*')
            ->from('h_event_subscriber')
            ->where(['is_deleted' => 0])
            ->createCommand()
            ->queryAll();
    }
}
