<?php

namespace member_eventconsumer\infrastructure\httpclient;

use member_eventconsumer\infrastructure\response\RemoteResponse;
use member_eventconsumer\infrastructure\signature\ISignature;
use member_eventconsumer\infrastructure\signature\SignDTO;
use member_eventlib\exception\SubscriberResponseException;
use member_eventlib\infrastructure\utils\XmlHelper;

class HttpClient implements IHttpClient
{
    const METHOD_POST = 'POST';

    /**
     * @var \member_eventconsumer\infrastructure\signature\ISignature
     */
    protected $signature;
    /**
     * @var array
     */
    protected $extraParams = [];

    protected $headers = [];

    protected $timeout = 5;

    protected $handle;

    public function __construct(ISignature $signature, $extraParams = [])
    {
        $this->signature = $signature;
        $this->extraParams = $extraParams;

        $this->handle = curl_init();
    }

    /**
     * post请求
     *
     * @param string $url
     * @param array $params
     * @param string $responseType json或xml
     * @return \member_eventconsumer\infrastructure\response\RemoteResponse
     */
    public function post($url, $params = [], $responseType = 'json')
    {
        $result = $this->httpRequest($url, $params, $responseType);

        return new RemoteResponse($result['return']['code'] ?: 200, $result['http_code'], $result['return']);
    }

    /**
     * key => value格式
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        if (is_array($headers) && $headers) {
            $this->headers = $headers;
        }
    }

    public function setHeader($key, $value)
    {
        if (is_string($key) && is_string($value)) {
            $this->headers[$key] = $value;
        }
    }

    protected function httpRequest($url, $params = [], $contentType = 'json', $headers = [], $method = self::METHOD_POST)
    {
        $url = rtrim($url, '/');

        if(is_array($headers) && !empty($headers)) {
            $request_headers = array_merge($this->headers, $headers);
        } else {
            $request_headers = $this->headers;
        }

        $signData = '';

        if(is_array($params) && !empty($params)) {
            if($contentType == 'json') {
                $request_headers['Content-Type'] = 'application/json';
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, ($signData = json_encode($params)));
            } elseif ($contentType == 'xml') {
                $request_headers['Content-Type'] = 'text/xml';
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, ($signData = XmlHelper::instance()->toXml($params)));
            }
        }

        switch($method) {
            case 'POST':
                curl_setopt($this->handle, CURLOPT_POST, true);
                break;
            case 'GET':
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, []);
                curl_setopt($this->handle, CURLOPT_POST, false);

                if(($signData = $params = http_build_query($params))) {
                    $url = $this->appendQs($url, $params);
                }
                break;
            default:
                curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->createCurlHeaderArray($request_headers));
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->handle, CURLOPT_CONNECTTIMEOUT, $this->timeout - 2);

        //额外的设置
        $this->setExtraCurlOption($this->handle);

        //签名
        if ($this->signature) {
            $signDTO = $this->signature->sign($signData);
            $url = $this->appendSign($url, $signDTO);
        }

        curl_setopt($this->handle, CURLOPT_URL, $url);

        $result = curl_exec($this->handle);

        //错误检查
        if(($curl_error = curl_error($this->handle)) !== '') {
            throw new \HttpRequestException($curl_error);
        }

        $resultArr = json_decode($result, true);

        if ($resultArr === null) {
            throw new SubscriberResponseException($result);
        }

        return [
            'http_code' => curl_getinfo($this->handle)['http_code'],
            'return' => $resultArr
        ];
    }

    protected function setExtraCurlOption(&$handle)
    {

    }

    private function createCurlHeaderArray($headers) {
        $curl_headers = [];

        foreach($headers as $key => $header) {
            $curl_headers[] = $key . ': ' . $header;
        }
        return $curl_headers;
    }

    private function appendSign($url, SignDTO $sign)
    {
        return $this->appendQs($url, [
            'sign' => $sign->sign(),
            'timestamp' => $sign->timestamp(),
            'nonce' => $sign->nonce()
        ]);
    }

    private function appendQs($url, $params)
    {
        return $url . (strpos($url, '?') === false ? '?' : '&')
        . (is_array($params) ? http_build_query($params) : $params);
    }

    public function __destruct()
    {
        @curl_close($this->handle);
    }
}
