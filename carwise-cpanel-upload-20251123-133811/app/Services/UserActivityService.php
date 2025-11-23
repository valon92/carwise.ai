<?php

namespace App\Services;

use App\Models\UserActivityLog;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityService
{
    /**
     * Log user activity.
     */
    public static function log(
        string $activityType,
        string $description,
        ?int $userId = null,
        ?string $sessionId = null,
        ?Request $request = null,
        ?array $requestData = null,
        ?array $responseData = null,
        ?int $responseStatus = null,
        ?int $executionTime = null,
        ?array $metadata = null
    ): UserActivityLog {
        $request = $request ?: request();
        $userId = $userId ?: Auth::id();
        $sessionId = $sessionId ?: session()->getId();

        // Get device and location information
        $deviceInfo = self::getDeviceInfo($request);
        $locationInfo = self::getLocationInfo($request);

        return UserActivityLog::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'activity_type' => $activityType,
            'activity_description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'request_data' => $requestData,
            'response_data' => $responseData,
            'response_status' => $responseStatus,
            'execution_time_ms' => $executionTime,
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'operating_system' => $deviceInfo['os'],
            'country' => $locationInfo['country'],
            'city' => $locationInfo['city'],
            'metadata' => $metadata
        ]);
    }

    /**
     * Log user login.
     */
    public static function logLogin(?int $userId = null, ?Request $request = null): UserActivityLog
    {
        return self::log(
            'login',
            'User logged in successfully',
            $userId,
            null,
            $request
        );
    }

    /**
     * Log user logout.
     */
    public static function logLogout(?int $userId = null, ?Request $request = null): UserActivityLog
    {
        return self::log(
            'logout',
            'User logged out',
            $userId,
            null,
            $request
        );
    }

    /**
     * Log page view.
     */
    public static function logPageView(string $page, ?int $userId = null, ?Request $request = null): UserActivityLog
    {
        return self::log(
            'page_view',
            "Viewed page: {$page}",
            $userId,
            null,
            $request,
            null,
            null,
            null,
            null,
            ['page' => $page]
        );
    }

    /**
     * Log diagnosis submission.
     */
    public static function logDiagnosisSubmit(int $diagnosisId, ?int $userId = null, ?Request $request = null): UserActivityLog
    {
        return self::log(
            'diagnosis_submit',
            'Submitted new diagnosis',
            $userId,
            null,
            $request,
            null,
            null,
            null,
            null,
            ['diagnosis_id' => $diagnosisId]
        );
    }

    /**
     * Log API call.
     */
    public static function logApiCall(
        string $endpoint,
        string $method,
        ?array $requestData = null,
        ?array $responseData = null,
        ?int $responseStatus = null,
        ?int $executionTime = null,
        ?int $userId = null,
        ?Request $request = null
    ): UserActivityLog {
        return self::log(
            'api_call',
            "API call to {$method} {$endpoint}",
            $userId,
            null,
            $request,
            $requestData,
            $responseData,
            $responseStatus,
            $executionTime,
            ['endpoint' => $endpoint, 'method' => $method]
        );
    }

    /**
     * Log error.
     */
    public static function logError(
        string $errorMessage,
        ?int $userId = null,
        ?Request $request = null,
        ?array $metadata = null
    ): UserActivityLog {
        return self::log(
            'error',
            $errorMessage,
            $userId,
            null,
            $request,
            null,
            null,
            null,
            null,
            $metadata
        );
    }

    /**
     * Get device information from request.
     */
    private static function getDeviceInfo(Request $request): array
    {
        $userAgent = $request->userAgent();
        
        // Simple device detection
        $deviceType = 'desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            $deviceType = 'mobile';
        } elseif (preg_match('/Tablet|iPad/', $userAgent)) {
            $deviceType = 'tablet';
        }

        // Browser detection
        $browser = 'Unknown';
        if (preg_match('/Chrome/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            $browser = 'Edge';
        }

        // OS detection
        $os = 'Unknown';
        if (preg_match('/Windows/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $os = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os
        ];
    }

    /**
     * Get location information from request.
     */
    private static function getLocationInfo(Request $request): array
    {
        // In a real application, you would use a geolocation service
        // For now, we'll return placeholder data
        return [
            'country' => null,
            'city' => null
        ];
    }

    /**
     * Create or update user session.
     */
    public static function createSession(int $userId, Request $request): UserSession
    {
        $sessionId = session()->getId();
        $deviceInfo = self::getDeviceInfo($request);
        $locationInfo = self::getLocationInfo($request);

        // Deactivate old sessions for this user
        UserSession::where('user_id', $userId)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return UserSession::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'operating_system' => $deviceInfo['os'],
            'country' => $locationInfo['country'],
            'city' => $locationInfo['city'],
            'is_active' => true,
            'last_activity_at' => now(),
            'expires_at' => now()->addHours(24) // 24 hour session
        ]);
    }

    /**
     * Update session activity.
     */
    public static function updateSessionActivity(string $sessionId): void
    {
        UserSession::where('session_id', $sessionId)
            ->where('is_active', true)
            ->update(['last_activity_at' => now()]);
    }

    /**
     * Get user activity summary.
     */
    public static function getUserActivitySummary(int $userId, int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        $activities = UserActivityLog::where('user_id', $userId)
            ->where('created_at', '>=', $startDate)
            ->get();

        $summary = [
            'total_activities' => $activities->count(),
            'login_count' => $activities->where('activity_type', 'login')->count(),
            'page_views' => $activities->where('activity_type', 'page_view')->count(),
            'diagnosis_submissions' => $activities->where('activity_type', 'diagnosis_submit')->count(),
            'api_calls' => $activities->where('activity_type', 'api_call')->count(),
            'errors' => $activities->where('activity_type', 'error')->count(),
            'most_used_browsers' => $activities->groupBy('browser')->map->count()->sortDesc()->take(5),
            'most_used_devices' => $activities->groupBy('device_type')->map->count()->sortDesc(),
            'activity_by_day' => $activities->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            })->map->count()
        ];

        return $summary;
    }
}
