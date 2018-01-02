<?php

namespace member_eventconsumer\repository;

use app\framework\biz\cache\OrganizationCacheManager;
use app\framework\utils\StringHelper;
use member_eventlib\event\IEvent;

/**
 * 事件消费日志仓储
 *
 * Class YiiEventLogRepository
 * @package member_eventconsumer\repository
 */
class YiiEventLogRepository extends EventLogRepository
{
    public function log(IEvent $event, $subscriberId, $msg, $result = 'success')
    {
        $this->db()->createCommand()->insert(
            'h_event_consume_log',
            [
                'id' => StringHelper::uuid(),
                'event_id' => $event->id(),
                'subscriber_id' => $subscriberId,
                'result' => $result,
                'msg' => $msg
            ]
        )->execute();
    }

    /**
     * 以千分之一的概率删除6个月之前的日志
     */
    public function garbageCollection()
    {
        if (mt_rand(1, 1000) == 100) {
            $this->db()->createCommand()->delete('h_event_consume_log', ['<', 'ctime', time() - 6 * 30 * 24 * 3600])->execute();
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
