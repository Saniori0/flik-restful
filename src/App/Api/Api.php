<?php


namespace Flik\Backend\App\Api;

use Flik\Backend\App\AbstractApp;

class Api extends AbstractApp
{

    public function __construct(protected readonly Router $router = new Router())
    {

    }

    public function getRouter(): Router
    {

        return $this->router;

    }

}