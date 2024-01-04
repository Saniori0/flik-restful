<?php


namespace Flik\Backend\Routing;

use Closure;
use ReflectionClass;

/**
 * A regular router. Can be used in models.
 * U can connect controllers if desired.
 * Used in App\Api\Router.
 *
 * It could be implemented with "interlayer" between Router and Routes.
 * But for the sake of simplicity, I limited myself to a simple array of Routes.
 */
class Router
{

    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    public function __construct(public readonly Hooker $hooker = new Hooker())
    {
    }

    /**
     * Creates a route.
     * See Routing\Route
     * @param string $path
     * @param Closure $callback
     * @param array $options see Routing\Route::Options
     * @return Route
     */
    public function route(string $path, Closure $callback, array $options = []): Route
    {

        $path = trim($path, "/");

        $route = new Route($path, $callback);
        $route->setOptions($options);

        $this->routes[$path] = $route;

        return $route;

    }

    /**
     * Connects the controller using class extending Routing\AbstractController
     * In the Routing\AbstractController described some points
     * on the implementation of this method and controllers in general.
     * @param string $controllerName Controller Name with namespace and double slash like a: Help\\Im\\Being\\Held\\Hostage\\ControllerClassName
     * @return void
     */
    public function controller(string $controllerName): void
    {

        if (!class_exists($controllerName)) return;

        $controller = new $controllerName;

        if (!($controller instanceof AbstractController)) return;

        $reflection = new ReflectionClass($controller);
        $staticReflectionMethods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);

        foreach ($staticReflectionMethods as $reflectionMethod) {

            $methodClosure = $reflectionMethod->getClosure();
            $reflectionAttributes = $reflectionMethod->getAttributes();

            foreach ($reflectionAttributes as $reflectionAttribute) {

                $reflectionAttributeNameExploded = explode("\\", $reflectionAttribute->getName());
                $reflectionAttributeShortName = end($reflectionAttributeNameExploded);

                switch ($reflectionAttributeShortName) {

                    case "Route":

                        $arguments = $reflectionAttribute->getArguments();

                        $path = $arguments[1];
                        $options = $arguments[2];

                        if (!isset($path, $options)) throw new \ArgumentCountError("Controller {{$controllerName}} -> Route {{$reflectionMethod->getName()}()}");
                        if (!is_string($path)) throw new \TypeError("Controller {{$controllerName}} -> Route {{$reflectionMethod->getName()}()} Path must be string");
                        if (!is_array($options)) throw new \TypeError("Controller {{$controllerName}} -> Route {{$reflectionMethod->getName()}()} Options must be array");

                        $Route = $this->route($path, $methodClosure, $options);

                        break;

                    default:

                        break;

                }

            }

        }

    }

    /**
     * @param string $query path of routes
     * @return Route|null
     */
    public function findRouteByQuery(string $query): ?Route
    {

        $query = trim($query, "/");

        $explodedQueryBySlash = explode("/", $query);
        $countExplodedQueryBySlash = count($explodedQueryBySlash);

        $routes = $this->routes;

        foreach ($routes as $routePath => $route) {

            $explodedPathBySlash = explode("/", $routePath);
            $countExplodedPathBySlash = count($explodedPathBySlash);

            if ($countExplodedQueryBySlash != $countExplodedPathBySlash) {

                $route = null;
                continue;

            }

            foreach ($explodedPathBySlash as $index => $pathValue) {

                $queryValue = $explodedQueryBySlash[$index];

                if (!$queryValue) {

                    break;

                }

                if ($pathValue[0] == ":") {

                    if ($queryValue[0] != ":") {

                        $route = null;
                        break;

                    }

                    $pathValue = ltrim($pathValue, ":");
                    $queryValue = ltrim($queryValue, ":");

                    $paramIndex = $pathValue;

                    if (str_contains($pathValue, "@")) {

                        $explodedPathValueByAt = explode("@", $pathValue);
                        $paramIndex = $explodedPathValueByAt[0];

                        if (count($explodedPathValueByAt) == 2) {

                            $hook = $explodedPathValueByAt[1];

                            if (mb_strlen($hook) > 0) {

                                $explodeHookByArrow = explode("->", $hook);

                                $hookHead = $explodeHookByArrow[0];
                                $hookBody = $explodeHookByArrow[1];

                                $Hook = $this->hooker->find($hookHead);

                                if($Hook){

                                    $queryValue = $Hook->execute($hookBody, $queryValue);

                                }

                            }

                        }

                    }

                    $route->setParam($paramIndex, $queryValue);

                    if ($index == ($countExplodedPathBySlash - 1)) {

                        return $route;

                    }

                    continue;

                }

                if ($pathValue != $queryValue) {

                    $route = null;
                    break;

                }

                if ($index == $countExplodedPathBySlash - 1) {

                    return $route;

                }

            }

        }

        return $route;

    }

}