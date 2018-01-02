<?php

namespace member_eventconsumer\repository;

use member_eventlib\event\IEvent;
use app\framework\biz\cache\OrganizationCacheManager;

/**
 * 记录在租户库中
 *
 * Class YiiEventReposity
 * @package member_eventconsumer\repository
 */
class YiiEventRepository extends EventRepository
{
    protected $db;

    public function add(IEvent $event)
    {
        $this->db()->createCommand()->insert(
            'h_event',
            [
                'id' => $event->id(),
                'tenant' => $event->tenant(),
                'group' => $event->group(),
                'name' => $event->name(),
                'event_obj' => serialize($event)
            ]
        )->execute();
    }

    /**
     * 以千分之一的概率删除6个月之前的事件
     */
    public function garbageCollection()
    {
        if (mt_rand(1, 1000) == 100) {
            $this->db()->createCommand()->delete('h_event', ['<', 'ctime', time() - 6 * 30 * 24 * 3600])->execute();
        }
    }

    /**
     * @return \yii\db\Connection
     */
    protected function db()
    {
        if (!$this->db) {
            $this->db = OrganizationCacheManager::getTenantDbConn($this->tenant);
        }

        return $this->db;
    }
}
