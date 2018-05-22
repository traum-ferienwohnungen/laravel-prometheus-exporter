<?php
namespace traumferienwohnungen\PrometheusExporter;

use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

/**
 * Class LaravelController
 * @package traumferienwohnungen\PrometheusExporter
 */
class LumenController extends Controller
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
