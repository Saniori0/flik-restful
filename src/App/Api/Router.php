<?php


namespace Flik\Backend\App\Api;

use Closure;

class Router extends \Flik\Backend\Routing\Router
{


    /** These methods implements REST API routes by adding an HTTP request method at the beginning of the route path. */
    public function get(string $path, Closure $callback, array $options = [])
    {

        return $this->route("get/" . ltrim($path, "/"), $callback, $options);

    }

    public function post(string $path, Closure $callback, array $options = [])
    {

        return $this->route("post/" . ltrim($path, "/"), $callback, $options);

    }

    public function patch(string $path, Closure $callback, array $options = [])
    {

        return $this->route("patch/" . ltrim($path, "/"), $callback, $options);

    }

    public function put(string $path, Closure $callback, array $options = [])
    {

        return $this->route("put/" . ltrim($path, "/"), $callback, $options);

    }

    public function delete(string $path, Closure $callback, array $options = [])
    {

        return $this->route("delete/" . ltrim($path, "/"), $callback, $options);

    }

    public function findRoute(string $httpMethod, string $query)
    {

        $query = ltrim($query, '/');
        return $this->findRouteByQuery("$httpMethod/$query");

    }

}