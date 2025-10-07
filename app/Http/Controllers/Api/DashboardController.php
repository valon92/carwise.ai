<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisSession;
use App\Models\DiagnosisResult;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get comprehensive dashboard statistics for authenticated user
     */
    public function statistics(): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->id;

            // Basic statistics
            $totalDiagnoses = DiagnosisSession::where('user_id', $userId)->count();
            $completedDiagnoses = DiagnosisSession::where('user_id', $userId)
                ->where('status', 'completed')
                ->count();
            
            $totalCars = Car::where('user_id', $userId)->count();
            $activeCars = Car::where('user_id', $userId)
                ->where('status', 'active')
                ->count();

            // Time-based statistics
            $thisMonth = now()->month;
            $thisYear = now()->year;
            $thisWeek = now()->week;

            $diagnosesThisMonth = DiagnosisSession::where('user_id', $userId)
                ->whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)
                ->count();

            $diagnosesThisWeek = DiagnosisSession::where('user_id', $userId)
                ->whereRaw("strftime('%W', created_at) = ?", [$thisWeek])
                ->whereRaw("strftime('%Y', created_at) = ?", [$thisYear])
                ->count();

            // Performance metrics - using analysis completion time
            $averageResponseTime = DiagnosisSession::where('user_id', $userId)
                ->where('status', 'completed')
                ->join('diagnosis_results', 'diagnosis_sessions.id', '=', 'diagnosis_results.diagnosis_session_id')
                ->whereNotNull('diagnosis_results.analysis_completed_at')
                ->selectRaw('AVG(julianday(diagnosis_results.analysis_completed_at) - julianday(diagnosis_sessions.created_at)) * 24 * 60 as avg_minutes')
                ->value('avg_minutes');

            // Success rate
            $successRate = $totalDiagnoses > 0 ? round(($completedDiagnoses / $totalDiagnoses) * 100) : 0;

            // Monthly trend data
            $monthlyData = DiagnosisSession::where('user_id', $userId)
                ->whereRaw("strftime('%Y', created_at) = ?", [$thisYear])
                ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as count")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $monthlyDiagnoses = collect($months)->map(function ($month, $index) use ($monthlyData) {
                $monthNumber = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                return [
                    'month' => $month,
                    'value' => $monthlyData->get($monthNumber)->count ?? 0
                ];
            });

            // Common issues analysis
            $commonIssues = DiagnosisResult::whereHas('diagnosisSession', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereNotNull('likely_causes')
            ->get()
            ->flatMap(function ($result) {
                $causes = is_string($result->likely_causes) 
                    ? json_decode($result->likely_causes, true) 
                    : $result->likely_causes;
                
                return collect($causes)->pluck('title');
            })
            ->countBy()
            ->map(function ($count, $title) {
                return ['name' => $title, 'count' => $count];
            })
            ->values()
            ->sortByDesc('count')
            ->take(10);

            // Recent diagnoses
            $recentDiagnoses = DiagnosisSession::where('user_id', $userId)
                ->with(['result'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'session_id' => $session->session_id,
                        'make' => $session->make,
                        'model' => $session->model,
                        'year' => $session->year,
                        'status' => $session->status,
                        'created_at' => $session->created_at,
                        'result' => $session->result ? [
                            'problem_title' => $session->result->problem_title,
                            'severity' => $session->result->severity,
                            'confidence_score' => $session->result->confidence_score
                        ] : null
                    ];
                });

            // Cost savings estimation (mock calculation)
            $totalCostSaved = $completedDiagnoses * 150; // Average $150 saved per diagnosis

            return response()->json([
                'success' => true,
                'data' => [
                    'basic_stats' => [
                        'total_diagnoses' => $totalDiagnoses,
                        'completed_diagnoses' => $completedDiagnoses,
                        'total_cars' => $totalCars,
                        'active_cars' => $activeCars,
                        'success_rate' => $successRate,
                    ],
                    'time_stats' => [
                        'diagnoses_this_month' => $diagnosesThisMonth,
                        'diagnoses_this_week' => $diagnosesThisWeek,
                        'average_response_time' => round($averageResponseTime ?? 0, 2),
                    ],
                    'analytics' => [
                        'monthly_diagnoses' => $monthlyDiagnoses,
                        'common_issues' => $commonIssues,
                        'total_cost_saved' => $totalCostSaved,
                    ],
                    'recent_diagnoses' => $recentDiagnoses,
                    'user_info' => [
                        'role' => $user->role,
                        'experience_years' => $user->experience_years ?? 0,
                        'clients_helped' => $user->role === 'mechanic' ? $completedDiagnoses : 0,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard notifications for authenticated user
     */
    public function notifications(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Mock notifications - in real app, these would come from database
            $notifications = [
                [
                    'id' => 1,
                    'type' => 'info',
                    'title' => 'Welcome to CarWise AI!',
                    'message' => 'Your account has been created successfully.',
                    'time' => 'Just now',
                    'read' => false
                ],
                [
                    'id' => 2,
                    'type' => 'success',
                    'title' => 'Diagnosis Completed',
                    'message' => 'Your latest car diagnosis has been completed successfully.',
                    'time' => '2 hours ago',
                    'read' => false
                ],
                [
                    'id' => 3,
                    'type' => 'warning',
                    'title' => 'Maintenance Reminder',
                    'message' => '2 of your cars need scheduled maintenance soon.',
                    'time' => '1 day ago',
                    'read' => true
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
