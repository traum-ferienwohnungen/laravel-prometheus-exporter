<?php
namespace Traum-ferienwohnungen\PrometheusExporter\Middleware;

/**
 * Class LumenResponseTimeMiddleware
 * @package Traum-ferienwohnungen\PrometheusExporter\Middleware
 */
class LumenResponseTimeMiddleware extends AbstractResponseTimeMiddleware
{
    /**
     * Get route name
     *
     * @return string
     */
    protected function getRouteName()
    {
        $route_info = $this->request->route()[1];
        return array_key_exists('as', $route_info) ? $route_info['as']: 'unnamed';
    }
}
