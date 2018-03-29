<?php

Route::get('metrics', \Traum-ferienwohnungen\PrometheusExporter\LpeController::class . '@metrics')->name('metrics');
