<?php

namespace member_eventconsumer\infrastructure\response;

/**
 * 分发响应
 *
 * Class DispatchResponse
 * @package member_eventconsumer\infrastructure\response
 */
class DispatchResponse extends Response
{
    /**
     * @var int 总共需要分发的消费者数
     */
    private $total;
    /**
     * @var int 成功分发的数目
     */
    private $dispatchedNum;

    public function __construct($total = 0, $dispatchedNum = 0, $code = 200, $body = '')
    {
        $this->total = $total;
        $this->dispatchedNum = $dispatchedNum;

        parent::__construct($code, $body);
    }

    public function total($total = null)
    {
        if ($total !== null) {
            $this->total = $total;
        }

        return $this->total;
    }

    public function dispatchedNum($num = null)
    {
        if ($num !== null) {
            $this->dispatchedNum = $num;
        }

        return $this->dispatchedNum;
    }

    public function __toString()
    {
        return json_encode([
           'total' => $this->total,
            'dispatchedNum' => $this->dispatchedNum,
            'code' => $this->code(),
            'body' => $this->body()
        ]);
    }
}
