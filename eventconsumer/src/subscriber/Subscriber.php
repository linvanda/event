<?php

namespace member_eventconsumer\subscriber;

use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventlib\exception\SubscriberException;
use member_eventconsumer\subscriber\middleware\IMiddleware;

/**
 * 订阅者
 * Class Subscriber
 * @package member_eventconsumer\subscriber
 */
abstract class Subscriber
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string 订阅者名称
     */
    protected $name;

    /**
     * 订阅的事件列表，数组
     * 格式：
     * [
     *      'tenant' => [
     *          'eventgroup' => [
     *              'eventcata1', 'eventcata2'
     *          ]
     *      ]
     * ]
     * 其中tenant和eventcata可以为*，表示所有
     * @var array
     */
    protected $events;

    /**
     * @var array 中间件列表
     */
    protected $middlewares = [];

    /**
     * @var array 自定义数据
     */
    protected $customInfo = [];

    /**
     * Subscriber constructor.
     * @param string $id
     * @param string $name
     * @param array $events
     */
    public function __construct($id, $name, $events)
    {
        if (! is_array($events)) {
            throw new SubscriberException('tenants or events must be an array');
        }

        $this->id = $id;
        $this->name = $name;
        $this->events = $events;
    }

    public function id()
    {
        return $this->id;
    }

    /**
     * 注册中间件
     *
     * @param IMiddleware $middlewareName
     * @return void
     */
    public function registerMiddleware(IMiddleware $middleware)
    {
        $className = get_class($middleware);

        if (!array_key_exists($className, $this->middlewares)) {
            $this->middlewares[$className] = $middleware;
        }
    }

    /**
     * 消费
     * 先顺序执行所有中间件逻辑，然后根据情况结束执行或继续执行具体的消费者消费逻辑
     *
     * @param SubscriberEventWrapper $eventWrapper 事件
     * @param SubscriberResponse $response
     * @return void
     */
    public function consume(SubscriberEventWrapper $eventWrapper, SubscriberResponse &$response)
    {
        /**
         * 顺序执行中间件
         */
        foreach ($this->middlewares() as $middleware) {
            $result = $middleware($eventWrapper, $this);

            //中间件返回Response对象，则终止后续流程执行
            if ($result instanceof SubscriberResponse) {
                $response = $result;
                $response->appendBody($eventWrapper->data());
                return;
            } elseif ($result instanceof SubscriberEventWrapper) {
                $eventWrapper = $result;
            } else {
                throw new SubscriberException("middleware returned invalid type object");
            }
        }

        /**
         * 执行公有逻辑
         */
        static::internalConsume($eventWrapper, $response);

        //将消费者真正消费的数据详情返回
        $response->appendBody($eventWrapper->data());
    }

    /**
     * 获取/设置订阅者的私有定制数据（一般是在中间件里面用到）
     * @return array
     */
    public function customInfo($customInfo = null)
    {
        if ($customInfo !== null && is_array($customInfo)) {
            $this->customInfo = $customInfo;
        }

        return $this->customInfo;
    }

    public function subscribedEvents()
    {
        return $this->events;
    }

    /**
     * 所有中间件列表
     * @return array
     */
    protected function middlewares()
    {
        return $this->middlewares;
    }

    /**
     * 具体的消费者消费逻辑
     *
     * @param SubscriberEventWrapper $eventWrapperWrapper
     * @param SubscriberResponse $response
     * @return void
     */
    abstract protected function internalConsume(SubscriberEventWrapper $eventWrapperWrapper, SubscriberResponse &$response);
}
