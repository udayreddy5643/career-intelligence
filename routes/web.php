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

    // Week 7 Day 2: Module 1 Skill Gap Analysis (LlamaIndex + Groq Pipeline)
    Route::post('/cia/career/skill-gap', [CiaController::class, 'careerSkillGap'])->name('cia.career.skill-gap');

    // Week 7 Day 3: Module 2 Career Recommendation Engine (CrewAI Agent)
    Route::post('/cia/career/recommend', [CiaController::class, 'recommendCareer'])->name('cia.career.recommend');

    // Week 7 Day 4: Module 3 Internship Matching (ChromaDB Vector Search)
    Route::post('/cia/career/match-internships', [CiaController::class, 'matchInternships'])->name('cia.career.match-internships');

    // Week 7 Day 5: Module 4 Learning Path Generator (LangGraph Workflow)
    Route::post('/cia/career/learning-path', [CiaController::class, 'learningPath'])->name('cia.career.learning-path');
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
    
    // Candidate skill gap computation algorithm (supports both standard and RAG endpoints)
    Route::post('/skill-gap', [CiaController::class, 'careerSkillGap'])->name('skill-gap');

    // CrewAI Career Recommendations Endpoint
    Route::post('/recommend', [CiaController::class, 'recommendCareer'])->name('recommend');

    // ChromaDB Internship Vector Search Matching Endpoint
    Route::post('/match-internships', [CiaController::class, 'matchInternships'])->name('match-internships');

    // LangGraph 8-Week Learning Path Generator Endpoint
    Route::post('/learning-path', [CiaController::class, 'learningPath'])->name('learning-path');
});
