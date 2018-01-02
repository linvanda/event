<?php

namespace member_eventlib\infrastructure\utils;

class XmlHelper
{
    private $version = '1.0';
    private $encoding = 'UTF-8';
    private $root  = 'document';
    private $defaultTagName = 'item';
    /**
     * @var \XmlWriter
     */
    private $xml  = null;

    private function __construct()
    {
        $this->xml = new \XmlWriter();
    }

    public static function instance()
    {
        static $instance;

        if (! $instance) {
            $instance = new self;
        }

        return $instance;
    }

    /**
     * @param array $data
     * @param bool|false $eIsArray
     * @return string
     */
    public function toXml($data, $eIsArray = false) {
        if(!$eIsArray) {
            $this->xml->openMemory();
            $this->xml->startDocument($this->version, $this->encoding);
            $this->xml->startElement($this->root);
        }

        foreach($data as $key => $value){
            if (is_int($key)) {
                $key = $this->defaultTagName;
            }

            if(is_array($value)){
                $this->xml->startElement(strval($key));
                $this->toXml($value, TRUE);
                $this->xml->endElement();
                continue;
            }

            $this->xml->writeElement(strval($key), $value);
        }

        if(!$eIsArray) {
            $this->xml->endElement();
            return $this->xml->outputMemory(true);
        }
    }
}
