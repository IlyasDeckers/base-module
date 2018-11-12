<?php
namespace Clockwork\Base\Traits;

trait CallStatic {
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}