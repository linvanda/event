<?php

namespace member_eventlib\infrastructure\log;

use mSDK\log\services\MessageQueueLogService;

class YiiLog implements ILog
{

    public function error($msg)
    {
        $this->getLogHandle()->add('event', $msg);
    }

    public function warning($msg)
    {
        $this->getLogHandle()->add('event', $msg);
    }

    protected function getLogHandle()
    {
        $iMessageQueueLog = \Yii::$container->get(MessageQueueLogService::class);
        $iMessageQueueLog->setMessageId('event');
        $iMessageQueueLog->setJob('event');
        return $iMessageQueueLog;
    }
}
