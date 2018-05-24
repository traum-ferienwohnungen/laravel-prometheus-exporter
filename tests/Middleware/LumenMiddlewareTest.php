<?php


use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use Prometheus\Histogram;
use traumferienwohnungen\PrometheusExporter\Middleware\LumenResponseTimeMiddleware;

class LumenMiddlewareTest extends Orchestra\Testbench\TestCase
{
    public function testLumenResponseTimeMiddleware()
    {
        $mockHistogram = Mockery::mock(Histogram::class);
        $mockHistogram->shouldReceive('observe')->once();

        $mockRegistry = Mockery::mock(CollectorRegistry::class);
        $mockRegistry->shouldReceive('getOrRegisterHistogram')->once()->andReturn(
            $mockHistogram
        );

        $middleware = new LumenResponseTimeMiddleware($mockRegistry);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('route')->once()->andReturn($this->getTestRouteObject());
        $request->shouldReceive('getMethod')->once();

        $middleware->handle(
            $request, function(){
            return new \Illuminate\Http\Response();
        });
    }

    private function getTestRouteObject()
    {
        return [
            0 => true,
            1 => [
                'as' => 'root',
                0 => []
            ],
            2 => []
        ];
    }
}
