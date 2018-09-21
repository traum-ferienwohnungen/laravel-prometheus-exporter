<?php
namespace traumferienwohnungen\PrometheusExporter\Instrumentation;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class FPM implements Collectible
{
    /**
     * @return \Prometheus\MetricFamilySamples[]
     */
    function collect()
    {
        if (! function_exists('fpm_get_status')){
            return [];
        }
        $fpmStatus = fpm_get_status();
        $registry = new CollectorRegistry(new InMemory());
        $metrics_namespace = config('prometheus_exporter.fpm_metrics_namespace');
        
        $startTime = $registry->getOrRegisterGauge($metrics_namespace, 'start_time', 'start_time');
        $startTime->set($fpmStatus['start-time']);

        $acceptedConn = $registry->getOrRegisterGauge($metrics_namespace, 'accepted_conn', 'accepted_conn');
        $acceptedConn->set($fpmStatus['accepted-conn']);

        $listenQueue = $registry->getOrRegisterGauge($metrics_namespace, 'listen_queue', 'listen_queue');
        $listenQueue->set($fpmStatus['listen-queue']);

        $maxListenQueue = $registry->getOrRegisterGauge($metrics_namespace, 'max_listen_queue', 'max_listen_queue');
        $maxListenQueue->set($fpmStatus['max-listen-queue']);

        $listenQueueLen = $registry->getOrRegisterGauge($metrics_namespace, 'listen_queue_len', 'listen_queue_len');
        $listenQueueLen->set($fpmStatus['listen-queue-len']);

        $idleProcesses = $registry->getOrRegisterGauge($metrics_namespace, 'idle_processes', 'idle_processes');
        $idleProcesses->set($fpmStatus['idle-processes']);

        $activeProcesses = $registry->getOrRegisterGauge($metrics_namespace, 'active_processes', 'active_processes');
        $activeProcesses->set($fpmStatus['active-processes']);

        $maxActiveProcesses = $registry->getOrRegisterGauge($metrics_namespace, 'max_active_processes', 'max_active_processes');
        $maxActiveProcesses->set($fpmStatus['max-active-processes']);

        $maxChildrenReached = $registry->getOrRegisterGauge($metrics_namespace, 'max_children_reached', 'max_children_reached');
        $maxChildrenReached->set($fpmStatus['max-children-reached']);

        $slowRequests = $registry->getOrRegisterGauge($metrics_namespace, 'slow_requests', 'slow_requests');
        $slowRequests->set($fpmStatus['slow-requests']);

        $procStartTime = $registry->getOrRegisterGauge($metrics_namespace, 'proc_start_time', 'proc start time', ['pid']);
        $procRequests = $registry->getOrRegisterGauge($metrics_namespace, 'proc_requests', 'proc requests', ['pid']);
        foreach ($fpmStatus['procs'] as $proc){
            $procStartTime->set($proc['start-time'], [$proc['pid']]);
            $procRequests->set($proc['requests'], [$proc['pid']]);
        }
        return $registry->getMetricFamilySamples();
    }
}