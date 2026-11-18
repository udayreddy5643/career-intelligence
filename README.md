# Project 4: CIA Career Intelligence — Core Modules

**Cynaris Solutions — Full Stack Web Development Internship**  
**Candidate**: Uday Reddy (`udayreddy5643` / `b.udayreddy5643@gmail.com`)  
**Milestone**: Week 7 Day 1 (Architecture Design — Career Intelligence CIA Mode + API Contracts)  
**Branch**: `feat/cynaris-w7d1-uday-b`  

---

## 📌 Executive Summary

The **Career Intelligence Agent (CIA)** is an enterprise AI-augmented recruitment intelligence and candidate mentorship engine. It delivers automated candidate skill gap analysis, multi-mode AI guidance (Full Stack Mentor Mode, Code Mode, AI Engineer Mode, Career Mode), and a resilient, high-availability dual-LLM pipeline utilizing **Groq Cloud** as the primary inference engine, **OpenRouter AI** as the fallback engine, and a **Deterministic Local Knowledge Base** as an offline safety net.

---

## 🏗️ Architectural Overview

```
                        [ Candidate Web Browser / Client View ]
                                          │
                                          ▼
                         [ Candidate CIA Controller & API ]
                      (app/Http/Controllers/Web/CiaController)
                                          │
                                          ▼
                         [ Resilient CIA Engine Service ]
                             (app/Services/CiaService)
                                          │
                 ┌────────────────────────┼────────────────────────┐
                 ▼                        ▼                        ▼
       [ Groq Cloud API ]        [ OpenRouter API ]      [ Local Deterministic KB ]
       • Primary LLM             • Fallback LLM          • Offline Safety Net
       • llama-3.3-70b-versatile • llama-3.1-8b-instruct • cynaris-cia-v1.0
       • Timeout: 10s            • Timeout: 12s          • Latency: <1ms
       • Ultra-fast inference    • Multi-model failover  • Zero-Crash Guarantee
```

---

## 📂 Repository Directory Structure

```text
career-intelligence/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Web/
│   │           └── CiaController.php       # CIA controller & API contracts
│   └── Services/
│       └── CiaService.php                  # Dual-LLM resilience service & failover pipeline
├── config/
│   └── cia.php                             # CIA modes, LLM endpoints & ChromaDB settings
├── docs/
│   └── week7-day1-cia-architecture-contracts.md # Full architectural docs & viva answers
├── resources/
│   └── views/
│       └── candidate/
│           └── cia.blade.php               # CIA candidate portal interactive UI
├── routes/
│   └── web.php                             # Candidate routes & API contracts
├── cli_test_cia.php                        # CLI test suite (6/6 tests passing)
├── composer.json                           # PHP & PSR-4 autoloading configuration
├── .env.example                            # Environment variables template
└── README.md                               # Project documentation
```

---

## 🚀 Quick Start & CLI Verification

### 1. Run the Automated Verification Test Suite
```bash
docker run --rm -v "${PWD}:/app" -w /app composer:latest php cli_test_cia.php
```

### 2. Output
```text
====================================================================
🛡️  CYNARIS CIA CAREER INTELLIGENCE — TEST SUITE (WEEK 7 DAY 1)
Architecture: Groq Cloud (Primary) -> OpenRouter (Fallback) -> Local KB
====================================================================

[TEST 1] System Health & Provider API Contract (/api/cia/status) -> PASS
[TEST 2] CIA Interaction 1: Full Stack Mentor Mode               -> PASS
[TEST 3] Outage Simulation & Failover Resilience (force_fallback)-> PASS
[TEST 4] CIA Interaction 2: Code Mode (Edge Cases)               -> PASS
[TEST 5] Skill Gap Analysis Engine API Contract (/api/cia/gap)   -> PASS
[TEST 6] Input Boundary Validation (Empty Query Rejection)       -> PASS

🏆 TEST SUITE RESULT: 6 / 6 PASSED (100% SUCCESS)
```

---

## 💬 Viva Voce Key Questions & Summary

1. **How does your skill gap analysis calculate the gap score?**
   - Applies a weighted formula: Core Competencies (70% weight) + Elective Competencies (30% weight) against target role taxonomies.
2. **Why choose ChromaDB for vector storage? Alternatives?**
   - ChromaDB provides lightweight in-memory and client-server embedding indexing (`all-MiniLM-L6-v2`, 384 dimensions) with zero setup friction. Alternatives include `pgvector` (ACID relational), `Pinecone` (managed serverless), and `Qdrant`/`Milvus` (distributed).
3. **How to scale Career Intelligence CIA to 10,000 users?**
   - Asynchronous queue workers (Redis/RabbitMQ), multi-tier Redis caching (65% token cost reduction), horizontal autoscaling on AWS ECS/EKS behind an ALB, and Token Bucket rate limiting.
