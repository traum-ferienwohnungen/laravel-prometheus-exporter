<?php
namespace traumferienwohnungen\PrometheusExporter;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;

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
                    if (!ini_get('apc.enable_cli') || !(extension_loaded('apc') || extension_loaded('apcu'))) {
                        throw new \ErrorException(
                            'Registered apc adapter, but apc is disabled. Set apc.enable_cli=1 to fix this');
                    }
                }
                $this->app->bind(Adapter::class, APC::class);
                break;
            case 'redis':
                $this->app->bind(Adapter::class, function(){
                    return new Redis(config('prometheus_exporter.redis'));
                });
                break;
            case 'array':
                $this->app->bind(Adapter::class, InMemory::class);
                break;
            default:
                throw new \ErrorException('"prometheus_exporter.adapter" must be either apc, redis or array');
        }

        $this->app->singleton(CollectorRegistry::class,
            function (){
                return new CollectorRegistry(app(Adapter::class));
            });
    }

}
