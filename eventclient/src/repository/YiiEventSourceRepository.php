<?php

namespace member_eventclient\repository;

use member_eventclient\source\EventSource;
use member_eventclient\ServiceProvider;
use yii\db\Query;

class YiiEventSourceRepository implements IEventSourceRepository
{
    private $cacheKey = 'member_event_source';

    public function all()
    {
        $cache = ServiceProvider::cache();

        if (
            (! defined('MEMBER_EVENT_DEBUG') || ! MEMBER_EVENT_DEBUG)
            && $cache->exists($this->cacheKey)
            && ($cacheSources = $cache->get($this->cacheKey))
        ) {
            return $cacheSources;
        }

        $list = [];
        foreach ($this->allFromDb() as $row) {
            $list[$row['flag']] = new EventSource($row['id'], $row['name'], $row['desc'], $row['flag']);
        }

        $cache->set($this->cacheKey, $list, 864000);

        return $list;
    }

    /**
     * @return array
     */
    private function allFromDb()
    {
        return (new Query())
            ->select('*')
            ->from('h_event_source')
            ->where(['is_deleted' => 0])
            ->createCommand()
            ->queryAll();
    }
}
