<?php

namespace traumferienwohnungen\PrometheusExporter\Instrumentation;


use Prometheus\CollectorRegistry;

abstract class AbstractCollector implements Collectible
{
    /**
     * @var CollectorRegistry
     */
    protected $registry;

    /**
     * AbstractCollector constructor.
     * @param CollectorRegistry $registry
     */
    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }
}
