<?php

namespace TraumFerienwohnungen\PrometheusExporter\Controller;

use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
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

        $volatileRegistry = new CollectorRegistry(new InMemory());
        /** @var Collectible $collectible */
        foreach(config('prometheus_exporter.active_collectibles') as $collectible_class){
            $collectible = new $collectible_class($volatileRegistry);
            if (! $collectible instanceof Collectible){
               throw new \RuntimeException("$collectible_class does not implement Collectible");
            }
            $collectible->collect();
        }
        $volatileMemorySamples = $volatileRegistry->getMetricFamilySamples();

        return response($renderer->render(array_merge($metricFamilySamples, $volatileMemorySamples)))
            ->header('Content-Type', $renderer::MIME_TYPE);
    }
}
