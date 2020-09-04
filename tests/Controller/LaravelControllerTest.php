<?php

class LaravelControllerTest extends Orchestra\Testbench\TestCase
{
    public function getPackageProviders($app)
    {
        return [
            'traumferienwohnungen\PrometheusExporter\LaravelServiceProvider'
        ];
    }

    public function testMetricsRoute()
    {
        $controller = new \traumferienwohnungen\PrometheusExporter\Controller\LaravelController();
        $response = $controller->metrics();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('opcache', $response->content());
    }

}
