<?php


namespace Flik\Backend\Routing;

class Hooker
{

    private array $hooks = [];
    private static Hooker $instance;

    public function __construct()
    {
    }

    public function find(string $index): false|Hook
    {

        return @$this->hooks[$index] ?: false;

    }

    public function hook(string $index, \Closure $callback)
    {

        return $this->hooks[$index] = new Hook($index, $callback);

    }

}