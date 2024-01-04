<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Flik\Backend\App\Api\Api;

$app = new Api();

$app->getRouter()->controller("\\Flik\\Backend\\App\\Api\\Dev\\Controllers\\ToDo");