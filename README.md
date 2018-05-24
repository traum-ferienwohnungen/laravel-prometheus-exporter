# laravel-prometheus-exporter

[![Build Status](https://travis-ci.org/traum-ferienwohnungen/laravel-prometheus-exporter.svg?branch=master)](https://travis-ci.org/traum-ferienwohnungen/laravel-prometheus-exporter)

A prometheus exporter for the Laravel and the Lumen web framework.

It tracks latency and request counts by request method, route and response code.

## Project State
This is unreleased software. I commit backwards incompatible changes without notice.

## Installation
`composer require traum-ferienwohnungen/laravel-prometheus-exporter`

### Adapters
Then choose from two storage adapters:
APCu is the default option. Redis can also be used.

#### APCu
Ensure apcu-bc is installed and enabled.

#### Redis
Ensure php redis is installed and enabled.

By default it looks for a redis server at localhost:6379. The server
can be configured in `config/prometheus_exporter.php`.

### Laravel
#### Enable the Middleware 
In `app/Http/Kernel.php`
```
protected $middleware = [
    ...
    \traumferienwohnungen\PrometheusExporter\Middleware\LaravelResponseTimeMiddleware::class,
];
```

#### Add an endpoint for the metrics
```
Route::get('metrics', \traumferienwohnungen\PrometheusExporter\LaravelController::class . '@metrics');
```

### Lumen
#### Track LUMEN_START time
In `public/index.php`
```
define('LUMEN_START', microtime(true));
```

#### Register the ServiceProvider
In `bootstrap/app.php`
```
$app->register(traumferienwohnungen\PrometheusExporter\LumenServiceProvider::class);
```

#### Add an endpoint for the metrics
In `bootstrap/app.php`
```
$app->router->get('metrics', ['as' => 'metrics', 'uses'=> 'traumferienwohnungen\PrometheusExporter\LumenController@metrics']);
```

## Configuration
The configuration can be found in `config/prometheus_exporter.php`.

| name        | description                                             |
|-------------|---------------------------------------------------------|
| adapter     | Storage adapter to use: 'apc' or 'redis' default: 'apc' |
| namespace   | name (prefix) to use in prometheus metrics. default: 'default' |
| namespace_http_server   | name (prefix) to use for http latency in prometheus metrics. default: 'http_server' |
| redis       | array of redis parameters. see prometheus_exporter.php for details |

