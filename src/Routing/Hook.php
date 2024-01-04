<?php


namespace Flik\Backend\Routing;

use Closure;

class Hook
{

    public function __construct(private string $index, private Closure $callback){}

    public function execute(string $body, string $input){

        $callback = $this->callback;
        return $callback($body, $input);

    }

}