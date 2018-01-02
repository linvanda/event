<?php

namespace member_eventconsumer\infrastructure\response;

/**
 * 远程调用响应体
 *
 * Class RemoteResponse
 * @package member_eventconsumer\infrastructure
 */
class RemoteResponse extends SubscriberResponse
{
    protected $businessCode;

    /**
     * RemoteResponse constructor.
     * @param int $businessCode 第三方系统返回的业务响应码。成功返回200，否则返回其他代码
     * @param int $httpCode http传输层的响应码
     * @param mixed $body
     */
    public function __construct($businessCode, $httpCode, $body)
    {
        $this->businessCode = intval($businessCode);

        parent::__construct($httpCode, $body);
    }

    public function businessCode()
    {
        return $this->businessCode;
    }
}
