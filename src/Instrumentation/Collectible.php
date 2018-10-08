<?php
namespace traumferienwohnungen\PrometheusExporter\Instrumentation;

use Prometheus\MetricFamilySamples;

interface Collectible
{
    /**
     * @return MetricFamilySamples[]
     */
    public function collect(): array;
}
