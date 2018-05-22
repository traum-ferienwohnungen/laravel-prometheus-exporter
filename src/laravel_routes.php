<?php

Route::get('metrics', \traumferienwohnungen\PrometheusExporter\LaravelController::class . '@metrics')->name('metrics');
