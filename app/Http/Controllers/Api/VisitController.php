<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string', 'max:180'],
            'visitor_id' => ['required', 'uuid'],
        ]);

        $path = $this->path($data['path']);

        if ($path === null) {
            return response()->json(['success' => true]);
        }

        $log = UserActivityService::log(
            'page_view',
            'Viewed '.$path,
            $this->accountId($request),
            $data['visitor_id'],
            $request,
            null,
            null,
            null,
            null,
            ['page' => $path]
        );

        $log->url = $path;
        $log->method = 'GET';
        $log->save();

        return response()->json(['success' => true]);
    }

    private function path(string $value): ?string
    {
        $path = parse_url($value, PHP_URL_PATH);
        $path = is_string($path) ? $path : $value;
        $path = '/'.ltrim(str_replace('\\', '/', $path), '/');

        if (str_contains($path, '..') || str_starts_with($path, '/api') || ! preg_match('#^/[A-Za-z0-9_./-]*$#', $path)) {
            return null;
        }

        return $path === '//' ? '/' : $path;
    }

    private function accountId(Request $request): ?int
    {
        try {
            return $request->user('sanctum')?->id;
        } catch (\Throwable) {
            return null;
        }
    }
}
