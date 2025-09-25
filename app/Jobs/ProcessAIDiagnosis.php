<?php

namespace App\Jobs;

use App\Models\DiagnosisSession;
use App\Services\AIDiagnosisService;
use App\Services\MonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessAIDiagnosis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry delays

    protected $sessionId;
    protected $diagnosisData;

    /**
     * Create a new job instance.
     */
    public function __construct(string $sessionId, array $diagnosisData)
    {
        $this->sessionId = $sessionId;
        $this->diagnosisData = $diagnosisData;
    }

    /**
     * Execute the job.
     */
    public function handle(AIDiagnosisService $aiService, MonitoringService $monitoring): void
    {
        $startTime = microtime(true);
        
        try {
            Log::info("Starting AI diagnosis processing", [
                'session_id' => $this->sessionId,
                'user_id' => $this->diagnosisData['user_id'] ?? null
            ]);

            // Update session status to processing
            $session = DiagnosisSession::where('session_id', $this->sessionId)->first();
            if (!$session) {
                throw new Exception("Diagnosis session not found: {$this->sessionId}");
            }

            $session->update(['status' => 'processing']);

            // Process with AI
            $result = $aiService->analyzeDiagnosis($this->diagnosisData);

            // Save result
            $session->result()->create([
                'problem_title' => $result['problem_title'],
                'problem_description' => $result['problem_description'],
                'severity' => $result['severity'],
                'confidence_score' => $result['confidence_score'],
                'likely_causes' => json_encode($result['likely_causes']),
                'recommended_actions' => json_encode($result['recommended_actions']),
                'estimated_costs' => json_encode($result['estimated_costs']),
                'ai_insights' => json_encode($result['ai_insights']),
                'related_issues' => json_encode($result['related_issues'] ?? []),
                'requires_immediate_attention' => $result['requires_immediate_attention'] ?? false,
                'ai_model_version' => $result['ai_model_version'] ?? '1.0',
                'ai_provider' => $result['ai_provider'] ?? 'unknown',
                'processing_time' => microtime(true) - $startTime
            ]);

            // Update session status to completed
            $session->update(['status' => 'completed']);

            $processingTime = microtime(true) - $startTime;
            
            Log::info("AI diagnosis completed successfully", [
                'session_id' => $this->sessionId,
                'processing_time' => $processingTime,
                'ai_provider' => $result['ai_provider'] ?? 'unknown'
            ]);

            // Log performance metrics
            $monitoring->logPerformance('ai_diagnosis_processing_time', $processingTime * 1000, [
                'session_id' => $this->sessionId,
                'ai_provider' => $result['ai_provider'] ?? 'unknown'
            ]);

        } catch (Exception $e) {
            Log::error("AI diagnosis processing failed", [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update session status to failed
            $session = DiagnosisSession::where('session_id', $this->sessionId)->first();
            if ($session) {
                $session->update(['status' => 'failed']);
            }

            // Log error
            $monitoring->logError("AI diagnosis processing failed: " . $e->getMessage(), [
                'session_id' => $this->sessionId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error("AI diagnosis job failed permanently", [
            'session_id' => $this->sessionId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Update session status to failed
        $session = DiagnosisSession::where('session_id', $this->sessionId)->first();
        if ($session) {
            $session->update(['status' => 'failed']);
        }
    }
}
