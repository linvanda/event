<?php

namespace member_eventclient\event;

use member_eventlib\event\BaseEvent;
use member_eventlib\exception\EventValidateException;
use member_eventlib\exception\InvalidOperationException;
use member_eventlib\infrastructure\IdGenerator;
use member_eventlib\infrastructure\utils\ObjectUtilTrait;
use member_eventclient\ServiceProvider;

/**
 * 领域事件基类
 *
 * 具体的领域事件应当写在各个项目的事件池中
 * 领域事件在发送之前（调用send）是可以修改属性的
 *
 * Class DomainEvent
 * @package member_eventclient\event
 */
abstract class DomainEvent extends BaseEvent
{
    /**
     * 使用对象助手实现一些便捷操作
     */
    use ObjectUtilTrait;

    /**
     * 本地（明源）事件发起方（事件来源）标识
     */
    const DEFAULT_SOURCE = '00000000-0000-0000-0000-000000000000';

    /**
     * 事件源id的前缀，可在子类中重写。如果设置了，则validate会检查所给的源是否符合该前缀
     */
    const SOURCE_PREFIX = '';

    private $canEdit = true;

    /**
     * 如果子类需要重写构造函数，不要忘了最后要调用父类构造函数
     *
     * DomainEvent constructor.
     * @param string $tenant 租户码
     * @param string $source 事件来源
     */
    public function __construct($tenant, $source = self::DEFAULT_SOURCE)
    {
        $this->id = IdGenerator::id();
        $this->tenant = $tenant;
        $this->source = $source;
        $this->timestamp = microtime(true);
    }

    /**
     * 发送事件
     * 此处委托事件发布器执行具体的发布操作
     */
    public function send()
    {
        $this->validate();

        $this->canEdit = false;

        ServiceProvider::eventPublisher()->publish($this);
    }

    /**
     * 只能在send之前修改属性
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name) && $this->canEdit) {
            $this->{$name} = $value;
        } else {
            throw new InvalidOperationException("can not set property after send for Event:" . $this->name());
        }
    }

    final public function data()
    {
        return [
            'type' => 'event',
            'id' => $this->id(),
            'group' => $this->group(),
            'name' => $this->name(),
            'tenant' => $this->tenant(),
            'timestamp' => $this->timestamp,
            'source' => $this->source(),
            'body' => $this->body()
        ];
    }

    /**
     * 具体的事件内容。
     * 默认实现：收集所有写有@data注解的属性值
     * 如果需要另行实现，请重写该方法
     *
     * @return array
     */
    protected function body()
    {
        return $this->collectData();
    }

    /**
     * 验证器
     * 子类可重写该验证器实现其它验证
     *
     * @throws EventValidateException
     */
    protected function validate()
    {
        $this->simpleValidate();
    }

    /**
     * 默认验证器
     *
     * @throws EventValidateException
     */
    private function simpleValidate()
    {
        if (! $this->tenant) {
            throw new EventValidateException("no tenant");
        }

        if (! $this->source) {
            throw new EventValidateException('no source');
        }

        if (static::SOURCE_PREFIX && strpos($this->source, static::SOURCE_PREFIX) !== 0) {
            throw new EventValidateException("invalid source,source must start with " . static::SOURCE_PREFIX);
        }

        foreach ($this->requiredProperties() as $property) {
            if ($this->{$property} === null) {
                throw new EventValidateException("property must not be null:$property");
            }
        }
    }
}
