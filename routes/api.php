<?php

use App\Http\Controllers\FingerprintController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Device/integration endpoints. Fingerprint hardware uses the web routes
| (/fingerprint-send, /fingerprint-check) with CSRF exceptions configured
| in VerifyCsrfToken. These aliases keep legacy /api/* clients working.
|
*/

Route::post('/fingerprint-store', [FingerprintController::class, 'send']);
Route::get('/fingerprint-list', [FingerprintController::class, 'show']);
