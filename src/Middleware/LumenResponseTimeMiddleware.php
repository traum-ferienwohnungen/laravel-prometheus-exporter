<?php
namespace traumferienwohnungen\PrometheusExporter\Middleware;
use Illuminate\Support\Facades\Route;

/**
 * Class LumenResponseTimeMiddleware
 * @package traumferienwohnungen\PrometheusExporter\Middleware
 */
class LumenResponseTimeMiddleware extends AbstractResponseTimeMiddleware
{
    /**
     * @return string[]
     */
    protected function getRouteNames()
    {
        $routeNames = [];
        foreach (Route::getRoutes() as $route) {
            $routeNames[] = $this->extractRouteName($route['action']);
        };
        return array_unique($routeNames);
    }

    /**
     * Get route name
     *
     * @return string
     */
    protected function getRouteName()
    {
        $route_info = $this->request->route();
        if ( NULL === $route_info){
            return 'unknown';
        }

        return $this->extractRouteName($route_info[1]);
    }

    /**
     * @param $routeInfo array
     * @return string
     */
    protected function extractRouteName($routeInfo)
    {
        return array_key_exists('as', $routeInfo) ? $routeInfo['as']: 'unnamed';
    }
}
