<?php

namespace member_eventconsumer\repository;

use member_eventconsumer\factory\SubscriberFactory;

/**
 * 测试用
 * Class TestSubscriberRepository
 * @package member_eventconsumer\repository
 */
class TestSubscriberRepository extends SubscriberRepository
{
    /**
     * @return array
     */
    protected function allSubscriber()
    {
        $conf = [
            'id' => '123',
            'name' => '测试订阅者',
            'url' => 'https://www.baidu.com',
            'data_type' => 'json',
            'events' => [ //订阅的事件列表。必须
                'retesting' => [
                    'member-room' => [
                        'event' => ['bind'],
                        'in_source' => ['000001*'],
                    ]
                ],
                'mysoft' => [
                    'member-room' => [
                        'event' => ['bind'],
                        'ex_source' => ['000001*']
                    ]
                ],
            ],
            'secret' => '2313133223',
            'cert_info' => [
                'cert' => '/cert/retesting/cert.pem',
                'cert_ca' => '/cert/retesting/ca-cert.cer',
                'cert_pwd' => 'weigaojf&123'
            ],
        ];
        $conf2 = [
            'id' => '456',
            'name' => '测试订阅者2',
            'data_type' => 'xml',
            'events' => [ //订阅的事件列表。必须
                '*' => [
                    'member-room-r' => [
                        'event' => ['*'],
                        //'in_source' => ['sourceid1', 'sourceid2'],//不接收这些来源方的事件。可选
                    ]
                ]
            ],
        ];

        return [
            '123' => SubscriberFactory::createFromConfData($conf),
            '456' => SubscriberFactory::createFromConfData($conf2),
        ];
    }
}
