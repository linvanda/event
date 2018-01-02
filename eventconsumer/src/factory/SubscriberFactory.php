<?php

namespace member_eventconsumer\factory;
use member_eventlib\exception\SubscriberException;
use member_eventconsumer\infrastructure\httpclient\HttpsClient;
use member_eventconsumer\infrastructure\httpclient\IHttpClient;
use member_eventconsumer\infrastructure\signature\ISignature;
use member_eventconsumer\ServiceProvider;
use member_eventconsumer\subscriber\middleware\IMiddleware;
use member_eventconsumer\subscriber\RemoteSubscriber;
use member_eventconsumer\subscriber\Subscriber;


/**
 * 订阅者工厂
 *
 * Class SubscriberFactory
 * @package member_eventconsumer\factory
 */
class SubscriberFactory
{
    protected static $instance;

    /**
     * 根据配置数组（一般从数据库获取）创建订阅者对象
     * 配置数据格式：
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
     *                //其中in_source和ex_source可以采用前缀匹配，如'000001*'
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
     *
     * @param array $config 配置数据
     * @return \member_eventconsumer\subscriber\Subscriber
     */
    public static function createFromConfData($config)
    {
        $factory = self::factory();

        if (
            ! $config['id'] ||
            ! $config['name'] ||
            ! is_array($config['events'])
        ) {
            throw new SubscriberException('error:params must not be null:id,name,tenants,events');
        }

        //根据有无url决定实例化LocalSubscriber还是RemoteSubscriber
        $subscriberClass = '\member_eventconsumer\subscriber\\';

        if ($config['url']) {
            $subscriberClass .= 'RemoteSubscriber';
        } else {
            $subscriberClass .= 'LocalSubscriber';
        }

        $subscriber = new $subscriberClass(
            $config['id'],
            $config['name'],
            $config['events']
        );

        if (! $subscriber instanceof Subscriber) {
            throw new SubscriberException('invalid subscriber');
        }

        //中间件
        if ($config['middlewares']) {
            if (is_array($config['middlewares'])) {
                foreach ($config['middlewares'] as $middlewareName) {
                    if (! is_string($middlewareName)) {
                        throw new SubscriberException('middleware name must be string:subscriber:' . $config['id']);
                    }

                    $subscriber->registerMiddleware($factory->createMiddleware($middlewareName));
                }
            } else {
                throw new SubscriberException('middleware config error:subscriber:' . $config['id']);
            }
        }

        //自定义信息
        if (is_array($config['customize_data']) && ! empty($config['customize_data'])) {
            $subscriber->customInfo($config['customize_data']);
        }

        //远程消费者的相关设置
        if ($subscriber instanceof RemoteSubscriber) {
            $factory->setRemoteInfo($subscriber, $config);
        }

        return $subscriber;
    }

    /**
     * 单例
     *
     * @return SubscriberFactory
     */
    public static function factory()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    protected function setRemoteInfo(RemoteSubscriber &$subscriber, $config)
    {
        if (! $config || ! is_array($config)) {
            throw new SubscriberException("invalid config for remote subscriber:" . print_r($config, true));
        }

        if (! $config['secret']) {
            throw new SubscriberException("remote subscriber need set secret");
        }

        if (! $config['sign']) {
            $config['sign'] = 'Signature';
        }

        if (! $config['client']) {
            $config['client'] = strpos($config['url'], 'https') === 0 ? 'HttpsClient' : 'HttpClient';
        }

        if (! is_array($config['client_extra_info'])) {
            $config['client_extra_info'] = [];
        }

        //httpclient
        $client = $this->createClient(
            $config['client'],
            $this->createSignature($config['sign'], $config['secret']),
            $config['client_extra_info']
        );

        //ssl证书信息
        if ($client instanceof HttpsClient && $config['cert_info']) {
            $client->certInfo($config['cert_info']);
        }

        $subscriber->client($client);
    }

    /**
     * 创建httpclient
     *
     * @param $clientName
     * @return \member_eventconsumer\infrastructure\httpclient\IHttpClient
     * @throws SubscriberException
     */
    protected function createClient($clientName, ISignature $sinature, $extraParams = [])
    {
        if (! is_string($clientName) || ! $clientName) {
            throw new \InvalidArgumentException('invalid argument,must pass string and can not be empty');
        }

        $realClassName = '\member_eventconsumer\infrastructure\httpclient\\'
            . str_replace('/', '\\', ltrim($clientName, '/'));

        if (class_exists($realClassName) && is_subclass_of($realClassName, IHttpClient::class)) {
            return new $realClassName($sinature, $extraParams);
        } else {
            throw new SubscriberException("invalid httpclient:$clientName");
        }
    }

    /**
     * 创建签名器
     *
     * @param string $signatureName
     * @param string $secret
     * @return ISignature
     * @throws SubscriberException
     */
    protected function createSignature($signatureName, $secret)
    {
        if (! is_string($signatureName) || ! $signatureName) {
            throw new \InvalidArgumentException('invalid argument,must pass string and can not be empty');
        }

        $realClassName = '\member_eventconsumer\infrastructure\signature\\'
            . str_replace('/', '\\', ltrim($signatureName, '/'));

        if (class_exists($realClassName) && is_subclass_of($realClassName, ISignature::class)) {
            $signature = new $realClassName;
            //设置秘钥
            $signature->secret($secret);

            return $signature;
        } else {
            throw new SubscriberException("invalid signature:$signatureName");
        }
    }

    /**
     * 根据中间件名称创建中间件
     *
     * @param $middlewareName
     * @return IMiddleware
     * @throws SubscriberException
     */
    protected function createMiddleware($middlewareName)
    {
        if (! is_string($middlewareName) || ! $middlewareName) {
            throw new \InvalidArgumentException('invalid argument,must pass string and can not be empty');
        }

        $realClassName = '\member_eventconsumer\subscriber\middleware\\'
            . str_replace('/', '\\', ltrim($middlewareName, '/'));

        if (class_exists($realClassName) && is_subclass_of($realClassName, IMiddleware::class)) {
            return new $realClassName;
        } else {
            throw new SubscriberException("invalid middlewarename:$middlewareName");
        }
    }
}
