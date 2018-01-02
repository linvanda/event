<?php

namespace member_eventlib\infrastructure;

/**
 * 明源的yii框架使用的消息队列包装器
 * Class YiiMessageQueue
 * @package member_eventlib\infrastructure
 */
class YiiMessageQueue implements IMessageQueue
{
    public function enqueue($queueName, $value)
    {
        \sdk\amqp\PublishV2::normal('MemberEvent/event/Consume', ['object' => base64_encode(serialize($value))], $queueName);
    }

    public function dequeue($queueName)
    {

    }
}
