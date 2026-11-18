<?php

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $val = getenv($key);
        return ($val !== false && $val !== null && $val !== '') ? $val : $default;
    }
}

return [
    /*
    |--------------------------------------------------------------------------
    | Career Intelligence Agent (CIA) — Operational Modes
    |--------------------------------------------------------------------------
    |
    | CIA operates across multiple specialized personas tailored for candidate
    | evaluation, mentorship, code review, and career intelligence.
    |
    */
    'modes' => [
        'mentor' => [
            'id' => 'mentor',
            'name' => 'Full Stack Mentor Mode',
            'icon' => 'fa-graduation-cap',
            'description' => 'Architectural design, full-stack mentorship, and comprehensive code review before committing.',
            'system_prompt' => "You are the Cynaris CIA Full Stack Mentor. You guide candidates through enterprise-grade software architecture, Clean Code standards, MVC design patterns, and code reviews. For every review request, evaluate: 1) Architecture & Separation of Concerns, 2) Error Handling & Resilience, 3) Code Cleanliness & Conventions, 4) Viva Voce readiness.",
        ],
        'code' => [
            'id' => 'code',
            'name' => 'Code Mode',
            'icon' => 'fa-code',
            'description' => 'Algorithm implementation, edge-case analysis, and bug elimination.',
            'system_prompt' => "You are the Cynaris CIA Code Assistant. You provide high-performance code snippets, uncover hidden edge cases, evaluate boundary conditions, and optimize database & API execution.",
        ],
        'ai_engineer' => [
            'id' => 'ai_engineer',
            'name' => 'AI Engineer Mode',
            'icon' => 'fa-brain',
            'description' => 'Prompt engineering, vector embedding strategies, ChromaDB indexing, and RAG pipelines.',
            'system_prompt' => "You are the Cynaris CIA AI Systems Architect. You specialize in LLM orchestration, dual-provider failover pipelines, ChromaDB vector retrieval, and production RAG reliability.",
        ],
        'career' => [
            'id' => 'career',
            'name' => 'Career Intelligence Mode',
            'icon' => 'fa-chart-line',
            'description' => 'Skill gap computation, ATS resume evaluation, and job role benchmarking.',
            'system_prompt' => "You are the Cynaris CIA Career Intelligence Engine. You calculate candidate skill gaps against target industry benchmarks (e.g., Full Stack Developer, Data Engineer) and recommend high-impact remediation pathways.",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary LLM Provider: Groq Cloud
    |--------------------------------------------------------------------------
    */
    'groq' => [
        'api_key' => env('GROQ_API_KEY', ''),
        'endpoint' => env('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'timeout' => (int) env('GROQ_TIMEOUT', 10),
        'temperature' => 0.3,
        'max_tokens' => 1024,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback LLM Provider: OpenRouter
    |--------------------------------------------------------------------------
    */
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY', ''),
        'endpoint' => env('OPENROUTER_ENDPOINT', 'https://openrouter.ai/api/v1/chat/completions'),
        'model' => env('OPENROUTER_MODEL', 'meta-llama/llama-3.1-8b-instruct:free'),
        'timeout' => (int) env('OPENROUTER_TIMEOUT', 12),
        'temperature' => 0.4,
        'max_tokens' => 1024,
        'headers' => [
            'HTTP-Referer' => env('APP_URL', 'http://localhost:8000'),
            'X-Title' => 'Cynaris Career Intelligence CIA',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vector Store: ChromaDB
    |--------------------------------------------------------------------------
    */
    'chromadb' => [
        'host' => env('CHROMADB_HOST', 'localhost'),
        'port' => (int) env('CHROMADB_PORT', 8000),
        'collection' => env('CHROMADB_COLLECTION', 'candidate_career_vectors'),
        'embedding_dimension' => 384, // all-MiniLM-L6-v2 standard
    ],

    /*
    |--------------------------------------------------------------------------
    | Deterministic Local Knowledge Engine (Zero-Crash Safety Net)
    |--------------------------------------------------------------------------
    */
    'offline_kb' => [
        'engine_name' => 'Cynaris Deterministic CIA Knowledge Engine',
        'version' => 'cynaris-cia-v1.0',
    ],
];
