<?php


namespace Flik\Backend\App\Api;

use Flik\Backend\App\AbstractApp;

class Api extends AbstractApp
{

    public function __construct(public readonly Router $router = new Router())
    {
    }

    public function dispatch()
    {

        // TODO Implement method

    }

}