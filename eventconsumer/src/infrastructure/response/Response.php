<?php

namespace member_eventconsumer\infrastructure\response;

/**
 * 响应
 * code兼容http code
 *
 * Class Response
 * @package member_eventconsumer\infrastructure
 */
class Response
{
    private $code;
    private $body = [];

    public function __construct($code = 500, $body = '')
    {
        $this->code = intval($code);
        if (isset($body) && $body !== '') {
            $this->body = [$body];
        }
    }

    /**
     * @param int|string $code
     * @return int
     */
    public function code($code = '')
    {
        if ($code) {
            $this->code = intval($code);
        }

        return $this->code;
    }

    /**
     * @param mixed $body
     * @return mixed
     */
    public function body($body = '')
    {
        if ($body) {
            $this->body = [$body];
        }

        return json_encode($this->body);
    }

    public function appendBody($data)
    {
        $this->body[] = $data;
    }

    public function __toString()
    {
        return json_encode([
            'code' => $this->code,
            'body' => $this->body
        ]);
    }
}
