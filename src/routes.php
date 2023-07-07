<?php

Route::get('csv', function () {
    echo "Hello from the csv package";

});

Route::get('add/{a}/{b}', [Ascentech\MassiveCsvImport\Controllers\MassiveCsvController::class, 'add']);

Route::get('subtract/{a}/{b}', [Ascentech\MassiveCsvImport\Controllers\MassiveCsvController::class, 'subtract']);