<?php

namespace member_eventconsumer\subscriber;

use member_eventlib\exception\SubscriberException;
use member_eventconsumer\infrastructure\httpclient\IHttpClient;
use member_eventconsumer\infrastructure\response\SubscriberResponse;

/**
 * 远程消费者
 * 推荐使用此消费者，将业务写在具体的业务系统里，事件系统本身不处理具体的业务逻辑，而是通过api将事件推送给第三方消费者
 *
 * Class RemoteSubscriber
 * @package member_eventconsumer\subscriber
 */
class RemoteSubscriber extends Subscriber
{
    /**
     * @var string 数据响应格式，json或xml
     */
    protected $responseType = 'json';

    /**
     * 第三方系统接收事件的url
     * @var string
     */
    protected $url;

    /**
     * 使用哪个httpClient执行api调用
     * @var \member_eventconsumer\infrastructure\httpclient\IHttpClient
     */
    protected $httpClient;

    public function responseType($type)
    {
        if (in_array($type, ['json', 'xml'])) {
            $this->responseType = $type;
        } else {
            throw new SubscriberException("invalid response type:$type");
        }
    }

    public function url($url)
    {
        $this->url = $url;
    }

    public function client(IHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * 消费逻辑
     * 将事件携带的业务数据组装成远程消费端需要的格式（xml或json)，推送给消费端系统
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param SubscriberResponse $response
     * @return void
     */
    protected function internalConsume(SubscriberEventWrapper $eventWrapper, SubscriberResponse &$response)
    {
        if (! $this->httpClient || ! ($this->httpClient instanceof IHttpClient)) {
            throw new SubscriberException('no httpclient can use');
        }

        if (! $this->url) {
            throw new SubscriberException('no uri can be invoked');
        }

        //发起http请求
        $remoteResponse = $this->httpClient->post($this->url, $eventWrapper->data(), $this->responseType);

        $response->code($remoteResponse->code());
        //将第三方系统的响应原样返回
        $response->body($remoteResponse);
    }
}
