<?php

use Illuminate\Support\Facades\Route;

Route::get('/up', function () {
    return response('OK', 200);
});

Route::view('/{any?}', 'app')->where('any', '^(?!api).*$');
