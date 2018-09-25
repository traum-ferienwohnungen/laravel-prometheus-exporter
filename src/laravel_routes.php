<?php

Route::get('metrics', \traumferienwohnungen\PrometheusExporter\Controller\LaravelController::class . '@metrics')->name('metrics');
