<?php

namespace member_eventconsumer\infrastructure\httpclient;

class HttpsClient extends HttpClient
{
    /**
     * @var array
     */
    protected $certInfo = [];

    public function certInfo($info)
    {
        $info['cert'] = $this->convertToAbsPath($info['cert']);
        $info['cert_ca'] = $this->convertToAbsPath($info['cert_ca']);

        $this->certInfo = $info;
    }

    protected function setExtraCurlOption(&$handle)
    {
        //设置ssl相关
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->handle, CURLOPT_SSL_VERIFYHOST, false);

        if ($this->certInfo) {
            //设置证书
            curl_setopt($handle, CURLOPT_SSLCERT, $this->certInfo['cert']);
            curl_setopt($handle, CURLOPT_SSLKEY, $this->certInfo['cert']);
            curl_setopt($handle, CURLOPT_CAINFO, $this->certInfo['cert_ca']);
            curl_setopt($handle, CURLOPT_SSLCERTPASSWD, $this->certInfo['cert_pwd']);
        }

        parent::setExtraCurlOption($handle);
    }

    private function convertToAbsPath($path)
    {
        if (strpos($path, 'http') !== 0 && $path[0] !== '/') {
            $path = __DIR__ . '/../../../assets/' . $path;
        }

        return $path;
    }
}
