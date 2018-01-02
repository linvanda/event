<?php

namespace member_eventconsumer\subscriber\middleware;

use member_eventconsumer\infrastructure\response\SubscriberResponse;
use member_eventconsumer\subscriber\middleware\plugin\Salesforce;
use member_eventconsumer\subscriber\Subscriber;
use member_eventconsumer\subscriber\SubscriberEventWrapper;

/**
 * 广万服务家专用中间件，调用服务家接口，返回Response对象
 *
 * Class FuwujiaMiddleware
 * @package member_eventconsumer\subscriber\middleware
 */
class FuwujiaMiddleware implements IMiddleware
{
    private $memberTypeMapping = [
        '一手业主' => 1,
        '二手业主' => 3,
        '同住人' => 2,
    ];

    private $houseMateMapping = [
        '租客' => 1,
        '朋友' => 2,
        '配偶' => 3,
        '父母' => 4,
        '子女' => 5,
        '其他' => 6
    ];

    /**
     * 具体的业务处理逻辑
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Subscriber $subscriber
     * @return SubscriberResponse
     */
    public function __invoke(SubscriberEventWrapper $eventWrapper, Subscriber $subscriber)
    {
        $config = $subscriber->customInfo();

        $client = new Salesforce(
            $config['login_url'],
            $config['key'],
            $config['secret'],
            $config['user'],
            $config['pwd'],
            $config['token']);

        switch ($eventWrapper->name()) {
            case 'bind':
                return $this->bindRoom($eventWrapper, $client, $config);
            case 'unbind':
                return $this->unbindRoom($eventWrapper, $client, $config);
            case 'logout':
                return $this->logout($eventWrapper, $client, $config);
        }
    }

    /**
     * 绑定房产
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Subscriber $subscriber
     * @param Salesforce $client
     * @param array $config
     * @return SubscriberResponse
     */
    private function bindRoom(SubscriberEventWrapper &$eventWrapper, Salesforce $client, $config)
    {
        $param = [
            'mId' => $eventWrapper['mid'],
            'memberType' => $this->memberTypeMapping[$eventWrapper['relation']] ?: 3,
            'houseList' =>[
                [
                    'roomGuid' => strtoupper($eventWrapper['room_id']),
                    'roomSapid' => ''
                ]
            ]
        ];

        if ($param['memberType'] == 2) {
            $param['houseMateType'] = $this->houseMateMapping[$eventWrapper['relation_with_owner']] ?: 6;
        }

        $response = $client->post($config['bind_room_url'], $param);
        $response['url'] = $client->lastRequestUri();

        return new SubscriberResponse($response['http_code'], $response);
    }

    /**
     * 解绑房产
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Subscriber $subscriber
     * @param Salesforce $client
     * @param array $config
     * @return SubscriberResponse
     */
    private function unbindRoom(SubscriberEventWrapper &$eventWrapper, Salesforce $client, $config)
    {
        $param = [
            'mId' => $eventWrapper['mid'],
            'identifyNo' => '',
            'identifyType' => '',
            'house' => [
                'roomGuid' => strtoupper($eventWrapper['room_id']),
                'roomSapid' => ''
            ]
        ];

        $response = $client->post($config['unbind_room_url'], $param);
        $response['url'] = $client->lastRequestUri();

        return new SubscriberResponse($response['http_code'], $response);
    }

    /**
     * 退出登录
     *
     * @param SubscriberEventWrapper $eventWrapper
     * @param Salesforce $client
     * @param $config
     */
    private function logout(SubscriberEventWrapper &$eventWrapper, Salesforce $client, $config)
    {
        $response = $client->post($config['logout_url'], $eventWrapper->data());
        $response['url'] = $client->lastRequestUri();

        return new SubscriberResponse($response['http_code'], $response);
    }
}
