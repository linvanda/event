<?php

namespace member_eventconsumer\infrastructure\httpclient;

use member_eventconsumer\infrastructure\signature\ISignature;

/**
 * http请求客户端接口
 *
 * Interface IHttpClient
 * @package member_eventconsumer\infrastructure\httpclient
 */
interface IHttpClient
{
    /**
     * IHttpClient constructor.
     * @param ISignature $signature 签名器
     * @param array $extraParams 额外参数，子类私有数据
     */
    public function __construct(ISignature $signature, $extraParams = []);

    /**
     * @param string $uri
     * @param array $params
     * @param string $responseType json或xml
     * @return \member_eventconsumer\infrastructure\response\RemoteResponse
     */
    public function post($uri, $params = [], $responseType = 'json');

    public function setHeaders($headers);

    public function setHeader($key, $value);
}
