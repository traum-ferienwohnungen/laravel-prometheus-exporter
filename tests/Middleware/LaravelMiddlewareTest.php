<?php


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\Histogram;
use traumferienwohnungen\PrometheusExporter\Middleware\LaravelResponseTimeMiddleware;

class LaravelMiddlewareTest extends Orchestra\Testbench\TestCase
{
    public function testLaravelResponseTimeMiddleware()
    {
        $mockHistogram = Mockery::mock(Histogram::class);
        $mockHistogram->shouldReceive('observe')->once();

        $mockRegistry = Mockery::mock(CollectorRegistry::class);
        $mockRegistry->shouldReceive('getOrRegisterHistogram')->once()->andReturn(
            $mockHistogram
        );

        $middleware = new LaravelResponseTimeMiddleware($mockRegistry);

        Route::shouldReceive('getRoutes');
        Route::shouldReceive('currentRouteName');
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('getMethod')->once();

        $middleware->handle(
            $requestMock, function(){
            return new \Illuminate\Http\Response();
        });
    }
}
