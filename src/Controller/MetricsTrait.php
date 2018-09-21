<?php

namespace TraumFerienwohnungen\PrometheusExporter\Controller;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use traumferienwohnungen\PrometheusExporter\Instrumentation\Collectible;

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
        $metricFamilySamples = $registry->getMetricFamilySamples();

        /** @var Collectible $collectible */
        foreach(config('prometheus_exporter.active_collectibles') as $collectible_class){
            $collectible = new $collectible_class();
            $metricFamilySamples = array_merge($metricFamilySamples, $collectible->collect());
        }

        return response($renderer->render($metricFamilySamples))
            ->header('Content-Type', $renderer::MIME_TYPE);
    }
}
