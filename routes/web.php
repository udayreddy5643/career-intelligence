<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\CiaController;

/*
|--------------------------------------------------------------------------
| Web Routes — Cynaris Career Intelligence CIA Mode
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::get('/', function () {
    return redirect()->route('candidate.cia');
});

Route::prefix('candidate')->name('candidate.')->group(function () {
    // Main Career Intelligence CIA Interactive Workspace
    Route::get('/cia', [CiaController::class, 'index'])->name('cia');
});

/*
|--------------------------------------------------------------------------
| API Contracts — Career Intelligence CIA Endpoints
|--------------------------------------------------------------------------
*/
Route::prefix('api/cia')->name('api.cia.')->group(function () {
    // Prompt routing through dual-LLM resilience pipeline (Groq -> OpenRouter -> Local KB)
    Route::post('/chat', [CiaController::class, 'chat'])->name('chat');
    
    // System health, provider operational states & telemetry
    Route::get('/status', [CiaController::class, 'status'])->name('status');
    
    // Candidate skill gap computation algorithm
    Route::post('/skill-gap', [CiaController::class, 'skillGap'])->name('skill-gap');
});
