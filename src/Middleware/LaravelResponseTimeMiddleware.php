<?php
namespace traumferienwohnungen\PrometheusExporter\Middleware;

/**
 * Class LaravelResponseTimeMiddleware
 * @package traumferienwohnungen\PrometheusExporter\Middleware
 */
class LaravelResponseTimeMiddleware extends AbstractResponseTimeMiddleware
{
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
