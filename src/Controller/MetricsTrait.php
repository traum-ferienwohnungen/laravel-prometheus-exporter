<?php

namespace TraumFerienwohnungen\PrometheusExporter\Controller;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

/**
 * Class MetricsTrait
 * @package traumferienwohnungen\PrometheusExporter
 */
trait MetricsTrait
{
    /**
     * metric
     *
     * Expose metrics for prometheus
     *
     * @return Response
     */
    public function metrics()
    {
        $renderer = new RenderTextFormat();

        $registry = app(CollectorRegistry::class);

        return response($renderer->render($registry->getMetricFamilySamples()))
            ->header('Content-Type', $renderer::MIME_TYPE);
    }
}
