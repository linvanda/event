<?php

return [
    //正式程序用(此处是默认配置，外界可以调用register方法覆盖此处的配置)
    'normal' => [
        'dispatch_publisher' => '\member_eventconsumer\publisher\DispatchPublisher',
        'dispatcher' => '\member_eventconsumer\dispatcher\EntrustDispatcher',
        'subscriber_repository' => '\member_eventconsumer\repository\YiiSubscriberRepository',
        'event_repository' => '\member_eventconsumer\repository\YiiEventRepository',
        'eventlog_repository' => '\member_eventconsumer\repository\YiiEventLogRepository',
        'subscriber_proxy' => '\member_eventconsumer\subscriber\SubscriberProxy',
        'health_inspector' => 'member_eventconsumer\subscriber\maintain\HealthInspector',
    ],
    //测试专用，继承normal的配置
    'unittest' => [
        'subscriber_repository' => '\member_eventconsumer\repository\TestSubscriberRepository',
        'event_repository' => '\member_eventconsumer\repository\TestEventRepository',
        'eventlog_repository' => '\member_eventconsumer\repository\TestEventLogRepository',
    ],
];
