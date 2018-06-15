<?php
namespace traumferienwohnungen\PrometheusExporter;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;

/**
 * Class LumenServiceProvider
 * @package traumferienwohnungen\PrometheusExporter
 */
class LumenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/config/config.php');

        $this->app->configure('prometheus_exporter');
        $this->mergeConfigFrom($source, 'prometheus_exporter');

        $this->app->middleware([
            'prometheusexporter' => 'traumferienwohnungen\PrometheusExporter\Middleware\LumenResponseTimeMiddleware'
        ]);

    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/config.php',
            'prometheus_exporter'
        );

        switch (config('prometheus_exporter.adapter')) {
            case 'apc':
                if ('cli' == php_sapi_name()) {
                    if (!ini_get('apc.enable_cli') || !extension_loaded('apc')) {
                        throw new \ErrorException(
                            'Registered apc adapter, but apc is disabled. Set apc.enable_cli=1 to fix this');
                    }
                }
                $this->app->bind('Prometheus\Storage\Adapter', 'Prometheus\Storage\APC');
                break;
            case 'redis':
                $this->app->bind('Prometheus\Storage\Adapter', function($app){
                    return new \Prometheus\Storage\Redis(config('prometheus_exporter.redis'));
                });
                break;
            default:
                throw new \ErrorException('"prometheus_exporter.adapter" must be either apc or redis');
        }

        $this->app->singleton(CollectorRegistry::class,
            function ($app){
                return new CollectorRegistry(app(\Prometheus\Storage\Adapter::class));
            });
    }

}
