<?php

namespace member_eventlib\infrastructure;

/**
 * 缓存：yii实现
 * Class YiiCache
 * @package member_eventlib\infrastructure
 */
class YiiCache implements ICache
{
    public function get($name)
    {
        return \Yii::$app->cache->get($name);
    }

    public function set($name, $value = null, $timeout = 0)
    {
        \Yii::$app->cache->set($name, $value, $timeout);
    }

    public function exists($name)
    {
        return \Yii::$app->cache->exists($name);
    }

    public function delete($name)
    {
        \Yii::$app->cache->delete($name);
    }
}
