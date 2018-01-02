<?php

class A
{
    public static function say()
    {
        static::f();
    }

    protected static function f()
    {
        echo 'A';
    }
}

class B extends A
{
    protected static function f()
    {
        echo 'B';
    }
}

B::say();