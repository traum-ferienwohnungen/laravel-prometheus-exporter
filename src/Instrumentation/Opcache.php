<?php
namespace traumferienwohnungen\PrometheusExporter\Instrumentation;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class Opcache implements Collectible
{
    /**
     * @return \Prometheus\MetricFamilySamples[]
     */
    function collect()
    {
        $opcache_status = opcache_get_status(false);
        $registry = new CollectorRegistry(new InMemory());
        $exported_values = [
            "opcache_enabled",
            "cache_full",
            "restart_pending",
            "restart_in_progress",
            "memory_usage.used_memory",
            "memory_usage.free_memory",
            "memory_usage.wasted_memory",
            "interned_strings_usage.buffer_size",
            "interned_strings_usage.used_memory",
            "interned_strings_usage.free_memory",
            "interned_strings_usage.number_of_strings",
            "opcache_statistics.num_cached_scripts",
            "opcache_statistics.num_cached_keys",
            "opcache_statistics.max_cached_keys",
            "opcache_statistics.hits",
            "opcache_statistics.start_time",
            "opcache_statistics.last_restart_time",
            "opcache_statistics.oom_restarts",
            "opcache_statistics.manual_restarts",
            "opcache_statistics.misses",
            "opcache_statistics.blacklist_misses",
        ];

        foreach ($exported_values as $exported_value){
            if (false !== ($dotPos = strpos($exported_value, "."))){
                $arrKey = substr($exported_value, 0, $dotPos);
                $subKey = substr($exported_value, $dotPos + 1);
                $label = str_replace('.', '_', $exported_value);
                $value = $opcache_status[$arrKey][$subKey];
            } else {
                $label = $exported_value;
                $value = $opcache_status[$exported_value];
            }

            $gauge = $registry->getOrRegisterGauge(config('prometheus_exporter.opcache_metrics_namespace'), $label, "");
            $gauge->set($value);
        }

        return $registry->getMetricFamilySamples();
    }
}