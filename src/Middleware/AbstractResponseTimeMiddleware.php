<?php
namespace traumferienwohnungen\PrometheusExporter\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Histogram;

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
     * @var Histogram
     */
    protected $requestDurationHistogram;

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
        if (defined('LARAVEL_START')){
            $start = LARAVEL_START;
        } elseif (defined('LUMEN_START')){
            $start = LUMEN_START;
        } else {
            $start = microtime(true);
        }
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
     */
    public function initRouteMetrics($routeNames)
    {
        $namespace = config('prometheus_exporter.namespace_http_server');
        $buckets = config('prometheus_exporter.histogram_buckets');

        $labelNames = $this->getRequestCounterLabelNames();

        $name = 'request_duration_milliseconds';
        $help = 'duration of http_requests';
        $this->requestDurationHistogram = $this->registry->getOrRegisterHistogram(
            $namespace, $name, $help, $labelNames, $buckets
        );
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
        $this->requestDurationHistogram->observe($duration_milliseconds, $labelValues);
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
