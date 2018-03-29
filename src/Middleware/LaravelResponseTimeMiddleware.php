<?php
namespace Traum-ferienwohnungen\PrometheusExporter\Middleware;

/**
 * Class LaravelResponseTimeMiddleware
 * @package Traum-ferienwohnungen\PrometheusExporter\Middleware
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
