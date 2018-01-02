<?php

namespace member_eventlib\infrastructure;
use member_eventlib\exception\MessageQueueException;

/**
 * 单元测试用消息队列包装器。使用数组模拟
 * Class YiiMessageQueue
 * @package member_eventlib\infrastructure
 */
class TestMessageQueue implements IMessageQueue
{
    private $queue = [];

    public function enqueue($queueName, $value)
    {
        if (!$this->queue[$queueName]) {
            $this->queue[$queueName] = [];
        }

        $this->queue[$queueName][] = $value;
    }

    public function dequeue($queueName)
    {
        if (!isset($this->queue[$queueName])) {
            throw new MessageQueueException("no such queue:$queueName");
        }

        if ($this->queue[$queueName]) {
            return array_shift($this->queue[$queueName]);
        }

        return null;
    }
}
