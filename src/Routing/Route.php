<?php


namespace Flik\Backend\Routing;

class Route
{

    private array $options = [];
    private array $params = [];

    public function __construct(private string $path, private \Closure $callback)
    {
    }

    public function setOptions(array $options)
    {

        $this->options = $options;

    }

    public function execute(mixed $data = []): mixed
    {

        $callback = $this->callback;

        return $callback((object)[
            "route" => $this,
            "summoner" => $data,
        ]);

    }

    public function setParam(string $key, string $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function setParams(array $params): Route
    {
        $this->params = $params;
        return $this;
    }

    public function getParam(string $key): string
    {
        return $this->params[$key];
    }

    public function getParams(): object
    {
        return (object)$this->params;
    }

}