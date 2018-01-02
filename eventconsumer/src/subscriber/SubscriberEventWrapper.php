<?php

namespace member_eventconsumer\subscriber;

use member_eventlib\event\IEvent;
use member_eventlib\exception\InvalidOperationException;

/**
 * 订阅者事件包裹器
 * 事件对象的各项属性本身是不可修改的，但订阅者可通过该包裹器实现（表面上）对事件对象数据的修改
 * 订阅者只能和该类实例打交道（而不是IEvent），该类作为IEvent的“伪装者”让订阅者认为其可以修改事件数据
 *
 * 该类实现了ArrayAccess接口，可以像php数组那样使用下标访问data数据(相当于调用data()方法)
 *
 * 该类的行为是设定好的，不可通过继承改变其行为
 * 为了保证其持有的event的只读性，该类不可修改event实例属性，亦不可对外暴露event实例
 *
 * Class SubscriberEventWrapper
 * @package member_eventconsumer\subscriber
 */
final class SubscriberEventWrapper implements \ArrayAccess
{
    /**
     * @var IEvent
     */
    private $event;
    private $data = [];
    private $mergedData = [];

    public function __construct(IEvent $event)
    {
        $this->event = $event;
    }

    /**
     * 订阅者可通过此方法获取|设置自定义的数据
     * 注意：通过key设置和获取的都是body中的值，而data()(无参数)获取的是整个值（包括头部）
     *
     * @param null $key
     * @param null $value
     * @param bool $unsetWhenNull 当$value=null时是否unset对应的key值。默认不unset，而是返回key值
     * @return array|SubscriberEventWrapper
     */
    public function data($key = null, $value = null, $unsetWhenNull = false)
    {
        if ($key && is_string($key)) {
            if (isset($value)) {
                //设置key的值
                $this->data[$key] = $value;

                $this->resetData();

                //设置值时支持链式调用
                return $this;
            } elseif ($value === null && $unsetWhenNull) {
                //unset对应的key值(实际上是将key值设为null,因为无法直接操作event的data，所以不能直接unset)
                $this->data[$key] = null;
                $this->resetData();
            } else {
                //访问key的值
                $body = $this->getMergedData()['body'];

                if (array_key_exists($key, $body)) {
                    return $body[$key];
                } else {
                    return null;
                }
            }
        } else {
            //访问整个data数组
            return $this->getMergedData();
        }
    }

    public function offsetExists($offset)
    {
        return $this->data($offset) !== null;
    }

    public function offsetGet($offset)
    {
        return $this->data($offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($offset && is_string($offset)) {
            $this->data($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        $this->data($offset, null, true);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed|void
     */
    public function __call($name, $arguments)
    {
        //不可通过包裹器发送事件
        if ($name != 'send') {
            return call_user_func_array([$this->event, $name], $arguments);
        } else {
            throw new InvalidOperationException("invalid call：send");
        }
    }

    public function __toString()
    {
        return json_encode($this->data());
    }

    private function resetData()
    {
        $this->mergedData = [];
    }

    private function getMergedData()
    {
        if (! $this->mergedData) {
            $mergedData = $this->event->data();
            $mergedData['body'] = array_replace($mergedData['body'], $this->data);

            $this->mergedData = $mergedData;
        }

        return $this->mergedData;
    }
}
