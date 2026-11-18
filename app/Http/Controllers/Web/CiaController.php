<?php

namespace App\Http\Controllers\Web;

use App\Services\CiaService;
use App\Services\SkillGapRagService;

/**
 * CIA Controller — Career Intelligence Agent Web & API Controller
 * 
 * Handles Candidate CIA Mode interactions, API contracts, prompt routing,
 * dual-LLM resilience failover, LlamaIndex RAG over skills taxonomy,
 * and ChromaDB candidate vector persistence.
 *
 * @package App\Http\Controllers\Web
 * @author Uday Reddy (b.udayreddy5643@gmail.com)
 */
class CiaController
{
    protected CiaService $ciaService;
    protected SkillGapRagService $skillGapRagService;

    public function __construct(?CiaService $ciaService = null, ?SkillGapRagService $skillGapRagService = null)
    {
        $this->ciaService = $ciaService ?: new CiaService();
        $this->skillGapRagService = $skillGapRagService ?: new SkillGapRagService($this->ciaService);
    }

    /**
     * Render the CIA Candidate Portal View.
     *
     * @return mixed
     */
    public function index()
    {
        $modes = [
            'mentor' => [
                'id' => 'mentor',
                'name' => 'Full Stack Mentor Mode',
                'icon' => 'fas fa-graduation-cap',
                'badge' => 'Pedagogical',
                'description' => 'Architectural design, system patterns, clean code principles, and PR code reviews.',
                'example' => 'Review my Dual-LLM failover architecture before I commit to git.',
            ],
            'code' => [
                'id' => 'code',
                'name' => 'Code Mode',
                'icon' => 'fas fa-code',
                'badge' => 'Engineering',
                'description' => 'Implementation assistance, edge-case analysis, and algorithm optimization.',
                'example' => 'What edge cases exist when primary LLM returns HTTP 429 rate limit?',
            ],
            'ai_engineer' => [
                'id' => 'ai_engineer',
                'name' => 'AI Engineer Mode',
                'icon' => 'fas fa-brain',
                'badge' => 'Systems AI',
                'description' => 'Prompt tuning, ChromaDB vector indexing, and RAG retrieval pipelines.',
                'example' => 'Why did we choose ChromaDB for vector storage and how does it compare to pgvector?',
            ],
            'career' => [
                'id' => 'career',
                'name' => 'Career Intelligence Mode',
                'icon' => 'fas fa-chart-line',
                'badge' => 'Analytics',
                'description' => 'Skill gap computation, resume benchmarking, and interview readiness scores.',
                'example' => 'Calculate my skill gap score for Full Stack Developer role.',
            ],
        ];

        $candidate = [
            'name' => 'Uday Reddy',
            'email' => 'b.udayreddy5643@gmail.com',
            'candidate_id' => 'CYN-2026-FS-0129',
            'track' => 'Full Stack Web Development Intern',
            'current_project' => 'Project 4: CIA Career Intelligence — Core Modules',
            'target_role' => 'Full Stack Software Engineer',
            'readiness_score' => 92,
            'skills' => ['PHP', 'Laravel', 'Docker', 'MariaDB', 'REST APIs', 'Groq LLM', 'OpenRouter', 'Git', 'ChromaDB'],
        ];

        if (function_exists('view')) {
            return view('candidate.cia', compact('modes', 'candidate'));
        }

        return [
            'view' => 'candidate.cia',
            'modes' => $modes,
            'candidate' => $candidate,
        ];
    }

    /**
     * API Contract: Dispatch Chat Query through Resilient Dual-LLM Pipeline.
     * Endpoint: POST /api/cia/chat
     *
     * @param mixed $request Request instance or array
     * @return array Standardized JSON API response
     */
    public function chat($request = null): array
    {
        // Extract inputs from Laravel Request or native globals
        $data = [];
        if (is_object($request) && method_exists($request, 'all')) {
            $data = $request->all();
        } elseif (is_array($request)) {
            $data = $request;
        } else {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true) ?: $_POST;
        }

        $message = $data['message'] ?? '';
        $mode = $data['mode'] ?? 'mentor';
        $context = $data['context'] ?? null;
        $forceFallback = filter_var($data['force_fallback'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (empty(trim($message))) {
            return [
                'status' => 'error',
                'code' => 422,
                'error' => 'Validation Failed: message is required and cannot be empty.',
                'timestamp' => date('c'),
            ];
        }

        return $this->ciaService->ask($message, $mode, $context, $forceFallback);
    }

    /**
     * API Contract: System Health & Provider Readiness Endpoint.
     * Endpoint: GET /api/cia/status
     *
     * @return array
     */
    public function status(): array
    {
        $hasGroqKey = !empty(getenv('GROQ_API_KEY'));
        $hasOrKey = !empty(getenv('OPENROUTER_API_KEY'));

        return [
            'status' => 'healthy',
            'timestamp' => date('c'),
            'service' => 'Cynaris CIA Career Intelligence Engine',
            'version' => '1.0.0',
            'providers' => [
                'primary' => [
                    'name' => 'Groq Cloud',
                    'model' => 'llama-3.3-70b-versatile',
                    'configured' => $hasGroqKey,
                    'status' => $hasGroqKey ? 'operational' : 'key_missing_using_fallback',
                ],
                'fallback' => [
                    'name' => 'OpenRouter AI',
                    'model' => 'meta-llama/llama-3.1-8b-instruct:free',
                    'configured' => $hasOrKey,
                    'status' => $hasOrKey ? 'operational' : 'key_missing_using_offline_kb',
                ],
                'offline_kb' => [
                    'name' => 'Cynaris Deterministic Safety Net',
                    'version' => 'cynaris-cia-v1.0',
                    'status' => 'always_available',
                ],
            ],
            'vector_store' => [
                'engine' => 'ChromaDB',
                'dimension' => 384,
                'status' => 'ready',
            ],
        ];
    }

    /**
     * API Contract: Skill Gap Analysis Calculation Endpoint.
     * Endpoint: POST /api/cia/skill-gap
     *
     * @param mixed $request
     * @return array
     */
    public function skillGap($request = null): array
    {
        $data = is_object($request) && method_exists($request, 'all') ? $request->all() : (is_array($request) ? $request : []);
        $targetRole = $data['target_role'] ?? 'Full Stack Developer';
        $candidateSkills = $data['skills'] ?? ['PHP', 'Laravel', 'Docker', 'MariaDB', 'REST APIs', 'Git'];

        // Benchmark requirement taxonomies
        $benchmarks = [
            'Full Stack Developer' => [
                'core' => ['PHP', 'Laravel', 'MariaDB', 'REST APIs', 'Docker', 'Git'],
                'elective' => ['Redis', 'Microservices', 'ChromaDB', 'Kubernetes', 'CI/CD'],
            ],
            'AI Systems Engineer' => [
                'core' => ['Python', 'ChromaDB', 'LLM Orchestration', 'Prompt Engineering', 'REST APIs'],
                'elective' => ['LangChain', 'Docker', 'Groq', 'FastAPI', 'Redis'],
            ],
        ];

        $benchmark = $benchmarks[$targetRole] ?? $benchmarks['Full Stack Developer'];
        
        $matchedCore = array_intersect(array_map('strtolower', $candidateSkills), array_map('strtolower', $benchmark['core']));
        $matchedElective = array_intersect(array_map('strtolower', $candidateSkills), array_map('strtolower', $benchmark['elective']));

        $coreScore = count($benchmark['core']) > 0 ? count($matchedCore) / count($benchmark['core']) : 1.0;
        $electiveScore = count($benchmark['elective']) > 0 ? count($matchedElective) / count($benchmark['elective']) : 1.0;

        // Weighted algorithm: 70% Core + 30% Elective
        $readinessScore = round(($coreScore * 0.70 + $electiveScore * 0.30) * 100, 1);
        $gapScore = round(100 - $readinessScore, 1);

        $missingCore = array_values(array_diff($benchmark['core'], array_map('ucfirst', $matchedCore)));
        $missingElective = array_values(array_diff($benchmark['elective'], array_map('ucfirst', $matchedElective)));

        return [
            'status' => 'success',
            'target_role' => $targetRole,
            'readiness_percentage' => $readinessScore,
            'gap_score_percentage' => $gapScore,
            'matched_skills' => array_values($candidateSkills),
            'missing_core_skills' => $missingCore,
            'missing_elective_skills' => $missingElective,
            'recommendation' => count($missingCore) === 0 
                ? 'Strong match for role. Focus on elective skills (ChromaDB, Microservices) for senior placement.'
                : 'Address core skill gaps before applying.',
            'timestamp' => date('c'),
        ];
    }

    /**
     * API Contract: LlamaIndex + Groq RAG Skill Gap Analysis Endpoint
     * Endpoint: POST /candidate/cia/career/skill-gap
     *
     * Inputs:
     * - candidate's completed lessons + quiz scores (from DB/request)
     * - target job role (from request)
     *
     * Returns structured JSON:
     * {
     *   "skill_gaps": [],
     *   "score": 0-100,
     *   "priority_learning": [],
     *   "domain_scores": {}
     * }
     *
     * @param mixed $request
     * @return array
     */
    public function careerSkillGap($request = null): array
    {
        $data = [];
        if (is_object($request) && method_exists($request, 'all')) {
            $data = $request->all();
        } elseif (is_array($request)) {
            $data = $request;
        } else {
            $rawInput = file_get_contents('php://input');
            $data = json_decode($rawInput, true) ?: $_POST;
        }

        $candidateId = $data['candidate_id'] ?? 'CYN-2026-FS-0129';
        $targetRole = $data['target_role'] ?? 'Full Stack Developer';
        $completedLessons = $data['completed_lessons'] ?? ['Week 1', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7 Day 1'];
        $quizScores = $data['quiz_scores'] ?? [
            ['lesson' => 'Week 6 Day 1: AI Tutor', 'score' => 100],
            ['lesson' => 'Week 6 Day 2: AI Quiz', 'score' => 90],
            ['lesson' => 'Week 6 Day 4: Query Audit', 'score' => 95],
            ['lesson' => 'Week 7 Day 1: CIA Architecture', 'score' => 92],
        ];
        $forceFallback = filter_var($data['force_fallback'] ?? false, FILTER_VALIDATE_BOOLEAN);

        return $this->skillGapRagService->analyze(
            $candidateId,
            $targetRole,
            $completedLessons,
            $quizScores,
            $forceFallback
        );
    }
}
