<?php

namespace member_eventconsumer\tests\factory;

use member_eventconsumer\factory\SubscriberFactory;
use member_eventlib\tests\TestCase;

class SubscriberFactoryTest extends TestCase
{
    public function testCreateMiddleware()
    {
        $middleware = $this->methodInvoke(SubscriberFactory::factory(), 'createMiddleware', ['TestMiddleware']);
        $this->assertInstanceOf(\member_eventconsumer\subscriber\middleware\IMiddleware::class, $middleware);
    }

    public function testCreateSignature()
    {
        $sign = $this->methodInvoke(SubscriberFactory::factory(), 'createSignature', ['Signature', '1234444']);
        $this->assertInstanceOf(\member_eventconsumer\infrastructure\signature\ISignature::class, $sign);
    }

    public function testCreateClient()
    {
        $sign = $this->methodInvoke(SubscriberFactory::factory(), 'createSignature', ['Signature', '1234444']);
        $client = $this->methodInvoke(SubscriberFactory::factory(), 'createClient', ['HttpsClient', $sign]);
        $this->assertInstanceOf(\member_eventconsumer\infrastructure\httpclient\HttpsClient::class, $client);
    }

    public function testCreateFromConfData()
    {
        /**
         * [
         *      'id' => '123456',//订阅者id，必须
         *      'name' => '明源物业',//订阅者名称，必须
         *      'url' => 'https://www.baidu.com', //第三方接收事件消息的url,可选
         *      'data_type' => 'json', //第三方接收事件的类型,json或xml,可选，默认json
         *      'events' => [ //订阅的事件列表。必须
         *              'tenant' => [
         *                  'eventgroup' => [
         *                          'event' => ['eventname1', 'eventname2'],
         *                          'in_source' => ['sourceid1', 'sourceid2'],//仅接收这些来源方的事件。可选
         *                          'ex_source' => ['sourceid1', 'sourceid2'],//不接收这些来源方的事件。可选
         *                      ]
         *                  ]
         *              ],//其中的tenant和eventname可以用*，分别表示所有租户和分组下的所有事件
         *      'secret' => '453454fff443f', //秘钥，用于数据签名。可选
         *      'cert_info' => [//https连接的ssl信息，可选
         *          'cert' => 'cert1.pem', //certificate证书路径，相对于eventlib/assets或绝对url(推荐)
         *          'cert_ca' => 'cert1.cer', //certificate的ca证书路径，相对于eventlib/assets或绝对url
         *          'cert_pwd' => '456dd' //certificate的password
         *      ],
         *      'client' => 'HttpsClient',  //指定使用哪个IHttpClient类发起请求（不同的第三方可能有不同的设置信息），可选。
         *                                  //类简名，相对于eventlib/src/infrastructure/httpclient/
         *                                  //默认根据url决定使用HttpClient或HttpsClient
         *      'client_extra_info' => ['myparam1' => 'value1'], //client的额外参数，作为IHttpClient构造函数的$extraParams传入，
         *                                                       //一般在要自己实现IHttpClient时作为额外参数使用。数组。可选
         *      'sign' => 'Signature', //指定使用哪个签名器签名，可选。默认使用Signature类。
         *                    //签名器必须在infrastructure/signature/目录或子目录下并实现ISignature接口
         *      'middlewares' => ['WgMiddleware'], //中间件列表，一维数组类简名，可选。
         *                                         //中间件必须是在subscriber/middleware/目录或其子目录下且实现IMiddleware接口
         *      'customize_data' => ['myvar1' => 'value1'], //其他自定义信息，数组，会原样传给Subscriber对象的customizeData属性
         * ]
         */
        $conf = [
               'id' => '123',
               'name' => '测试订阅者',
               'url' => 'https://www.baidu.com',
               'data_type' => 'json',
            'events' => [ //订阅的事件列表。必须
                    'retesting' => [
                       'member-room' => [
                               'event' => ['bind', 'unbind'],
                               'in_source' => ['sourceid1', 'sourceid2'],//仅接收这些来源方的事件。可选
                               'ex_source' => ['sourceid1', 'sourceid2'],//不接收这些来源方的事件。可选
                           ]
                   ]
               ],
               'secret' => '2313133223',
               'cert_info' => [
                   'cert' => '/cert/retesting/cert.pem',
                   'cert_ca' => '/cert/retesting/ca-cert.cer',
                   'cert_pwd' => 'weigaojf&123'
               ],
//               'client' => 'we',
//               'client_extra_info' => [],
//               'sign' => 'dd',
//               'middlewares' => ['ddf'],
//               'customize_data' => ''
        ];

        $sub = SubscriberFactory::createFromConfData($conf);

        $this->assertInstanceOf(\member_eventconsumer\subscriber\Subscriber::class, $sub);
    }
}
