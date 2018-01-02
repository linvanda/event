<?php

namespace member_eventconsumer;

use member_eventconsumer\dispatcher\DispatchPackage;
use member_eventconsumer\infrastructure\response\DispatchResponse;
use member_eventconsumer\infrastructure\response\Response;
use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventlib\event\maintain\IGarbageCollection;
use member_eventlib\IPackage;
use member_eventlib\event\IEvent;

/**
 * 调度器基类
 *
 * Class Schedular
 * @package member_eventconsumer\schedular
 */
class Schedular
{
    protected function __construct()
    {

    }

    public static function create()
    {
        static $schedular;

        if (!$schedular) {
            $schedular = new static;
        }

        return $schedular;
    }

    public function run(IPackage $package)
    {
        if ($package instanceof IEvent) {
            /**
             * 接收到event封包，持久化并交给分发器分发
             */
            $dispatchResponse = new DispatchResponse();

            ServiceProvider::eventRepository($package->tenant())->add($package);
            ServiceProvider::dispatcher()->dispatch($package, $dispatchResponse);

            //处理分发响应
            $this->dealDispatchResponse($dispatchResponse, $package);
        } elseif ($package instanceof DispatchPackage) {
            /**
             * 接收到分发封包，交给订阅者代理处理
             */
            if (! $package->isAwake()) {
                $package->sink();
            } else {
                $response = new SubscriberResponse();

                try {
                    ServiceProvider::subscriberProxy()->act($package, $response);
                } catch (\Exception $e) {
                    //抛异常，设置response对象
                    $response->code(500);
                    $response->body($e);
                }

                //处理订阅者代理响应
                $this->dealSubscriberResponse($response, $package);

                //记录日志
                ServiceProvider::eventLog($package->event()->tenant())
                    ->log(
                        $package->event(),
                        $package->subscriberFlag(),
                        $response,
                        strpos(strval($response->code()), '2') === 0 ? 'success' : 'error'
                    );
            }
        } else {
            ServiceProvider::logger()->error("invalid object type.object:" . print_r($package, true));
        }
    }

    /**
     * 处理订阅者代理的响应
     *
     * @param SubscriberResponse $response
     * @param DispatchPackage $dispatchPackage
     * @return void
     */
    protected function dealSubscriberResponse(SubscriberResponse $response, DispatchPackage $dispatchPackage)
    {
        //是否需要重发
        if (! $this->responseOk($response)) {
            $dispatchPackage->send();

            //做亚健康标记
            ServiceProvider::healthInspector()->markAsUnhealthy(
                ServiceProvider::subscriberRepository()->find($dispatchPackage->subscriberFlag())
            );
        }
    }

    /**
     * 处理分发器的响应
     *
     * @param DispatchResponse $response
     * @param IEvent $event
     */
    protected function dealDispatchResponse(DispatchResponse $response, IEvent $event)
    {
        //没有进行任何分发，标记为垃圾事件
        if (! $response->total()) {
            ServiceProvider::garbageCollection()->mark(
                $event->tenant(),
                $event->group(),
                $event->name(),
                $event->source(),
                IGarbageCollection::GARBAGE_UNSUBSCRIBED
            );
        }
    }

    private function responseOk(Response $response)
    {
        return strpos(strval($response->code()), '2') === 0;
    }
}
