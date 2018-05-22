<?php


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use traumferienwohnungen\PrometheusExporter\Middleware\LaravelResponseTimeMiddleware;

class LaravelMiddlewareTest extends Orchestra\Testbench\TestCase
{
    public function testLaravelResponseTimeMiddleware()
    {
        $mockCounter = Mockery::mock(Counter::class);
        $mockCounter->shouldReceive('inc')->once();
        $mockCounter->shouldReceive('incBy')->once();
        $mockRegistry = Mockery::mock(CollectorRegistry::class);
        $mockRegistry->shouldReceive('getOrRegisterCounter')->twice()->andReturn(
            $mockCounter
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
