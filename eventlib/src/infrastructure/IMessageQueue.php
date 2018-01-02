<?php

namespace member_eventlib\infrastructure;

/**
 * 消息队列接口
 * 此处一般是作为包装器使用，调用具体框架的消息队列实现
 *
 * Interface IMessageQueue
 * @package member_eventlib\infrastructure
 */
interface IMessageQueue
{
    /**
     * 入列
     * @param \string $queueName
     * @param mixed $value
     * @return void
     */
    public function enqueue($queueName, $value);

    /**
     * 出列
     * @param \string $queueName
     * @return mixed
     */
    public function dequeue($queueName);
}
