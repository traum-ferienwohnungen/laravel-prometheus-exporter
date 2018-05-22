<?php
namespace traumferienwohnungen\PrometheusExporter\Middleware;

/**
 * Class LaravelResponseTimeMiddleware
 * @package traumferienwohnungen\PrometheusExporter\Middleware
 */
class LaravelResponseTimeMiddleware extends AbstractResponseTimeMiddleware
{
    protected function getRouteNames()
    {
        $routeNames = [];
        foreach (\Route::getRoutes() as $route){
            $routeNames[] = $route->getName() ?: "unnamed";
        }
        return $routeNames;
    }

    /**
     * Get route name
     *
     * @return string
     */
    protected function getRouteName()
    {
        return \Route::currentRouteName() ?: 'unnamed';
    }
}
