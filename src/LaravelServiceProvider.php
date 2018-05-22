<?php
namespace traumferienwohnungen\PrometheusExporter;

use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use traumferienwohnungen\PrometheusExporter\Middleware\LaravelResponseTimeMiddleware;

/**
 * Class LaravelServiceProvider
 * @package traumferienwohnungen\PrometheusExporter
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @param Kernel $kernel
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        $source = realpath(__DIR__ . '/config/config.php');
        $this->publishes([$source => config_path('prometheus_exporter.php')]);
        $this->mergeConfigFrom($source, 'prometheus_exporter');

        $kernel->pushMiddleware(LaravelResponseTimeMiddleware::class);
        $this->registerMetricsRoute();
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
    }

    public function registerMetricsRoute()
    {
        $this->loadRoutesFrom(__DIR__ . '/laravel_routes.php');
    }
}
