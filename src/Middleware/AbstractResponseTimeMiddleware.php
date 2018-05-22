<?php
namespace traumferienwohnungen\PrometheusExporter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;

/**
 * Class AbstractResponseTimeMiddleware
 * @package traumferienwohnungen\PrometheusExporter
 */
abstract class AbstractResponseTimeMiddleware
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CollectorRegistry
     */
    protected $registry;

    /**
     * @var Counter
     */
    protected $requestCounter;

    /**
     * @var Counter
     */
    protected $requestDurationCounter;

    /**
     * AbstractResponseTimeMiddleware constructor.
     * @param CollectorRegistry $registry
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
        $this->initRouteMetrics($this->getRouteNames());
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $this->request = $request;

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $route_name = $this->getRouteName();
        $method = $request->getMethod();
        $status = $response->getStatusCode();

        $duration = microtime(true) - $start;
        $duration_milliseconds = $duration * 1000.0;
        $this->countRequest($route_name, $method, $status, $duration_milliseconds);

        return $response;
    }

    /**
     * @param $routeNames string[]
     * @throws \Prometheus\Exception\MetricsRegistrationException
     */
    public function initRouteMetrics($routeNames)
    {
        $namespace = config('prometheus_exporter.namespace_http_server');
        $labelNames = $this->getRequestCounterLabelNames();

        $name = 'requests_total';
        $help = 'number of http requests';
        $this->requestCounter = $this->registry->getOrRegisterCounter($namespace, $name, $help, $labelNames);

        $name = 'requests_latency_milliseconds';
        $help = 'duration of http_requests';
        $this->requestDurationCounter = $this->registry->getOrRegisterCounter($namespace, $name, $help, $labelNames);

        foreach ($routeNames as $route) {
            foreach (config('prometheus_exporter.init_metrics_for_http_methods') as $method) {
                foreach (config('prometheus_exporter.init_metrics_for_http_status_codes') as $statusCode) {
                    $labelValues = [(string)$route, (string)$method, (string) $statusCode];
                    $this->requestCounter->incBy(0, $labelValues);
                    $this->requestDurationCounter->incBy(0.0, $labelValues);
                }
            }
        }
    }

    protected function getRequestCounterLabelNames()
    {
        return [
            'route', 'method', 'status_code',
        ];
    }

    public function countRequest($route, $method, $statusCode, $duration_milliseconds)
    {
        $labelValues = [(string)$route, (string)$method, (string) $statusCode];
        $this->requestCounter->inc($labelValues);
        $this->requestDurationCounter->incBy($duration_milliseconds, $labelValues);
    }

    /**
     * Get metric family samples
     *
     * @return \Prometheus\MetricFamilySamples[]
     */
    public function getMetricFamilySamples()
    {
        return $this->registry->getMetricFamilySamples();
    }

    /**
     * @return string[]
     */
    abstract protected function getRouteNames();

    /**
     * Get route name
     *
     * @return string
     */
    abstract protected function getRouteName();
}
