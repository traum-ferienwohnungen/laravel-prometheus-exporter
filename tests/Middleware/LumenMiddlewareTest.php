<?php


use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Prometheus\Counter;
use traumferienwohnungen\PrometheusExporter\Middleware\LumenResponseTimeMiddleware;

class LumenMiddlewareTest extends Orchestra\Testbench\TestCase
{
    public function testLumenResponseTimeMiddleware()
    {
        $counter = Mockery::mock(Counter::class);
        $counter->shouldReceive('inc')->once();
        $counter->shouldReceive('incBy')->once();
        $registry = Mockery::mock(CollectorRegistry::class);
        $registry->shouldReceive('getOrRegisterCounter')->twice()->andReturn(
            $counter
        );

        $middleware = new LumenResponseTimeMiddleware($registry);

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
