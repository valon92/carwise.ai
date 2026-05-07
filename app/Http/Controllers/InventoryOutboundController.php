<?php

namespace App\Http\Controllers;

use App\Models\ReferralInventoryClick;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InventoryOutboundController extends Controller
{
    /**
     * Secure handoff to a dealer listing (e.g. MarketCheck VDP). No open redirects:
     * destination URL must have been stored server-side when the carousel was built.
     * Commercial revenue share requires separate agreements with sellers / data providers.
     */
    public function redirect(Request $request, string $token): RedirectResponse
    {
        if (! preg_match('/^[a-zA-Z0-9]{32,64}$/', $token)) {
            abort(404);
        }

        $cacheKey = 'inventory_out:'.$token;
        $payload = Cache::pull($cacheKey);

        if (! is_array($payload) || empty($payload['v']) || ! is_string($payload['v'])) {
            abort(404);
        }

        $url = $payload['v'];
        $listingRef = is_string($payload['ref'] ?? null) ? $payload['ref'] : 'unknown';

        if (! $this->isAllowedOutboundUrl($url)) {
            Log::warning('inventory.outbound.rejected_url', ['ref' => $listingRef]);

            abort(404);
        }

        try {
            ReferralInventoryClick::create([
                'listing_ref' => Str::limit($listingRef, 80),
                'destination_host' => parse_url($url, PHP_URL_HOST),
                'user_id' => $request->user()?->id,
                'ip_hash' => hash('sha256', ($request->ip() ?? '').'|'.config('app.key')),
            ]);
        } catch (\Throwable $e) {
            Log::error('inventory.outbound.log_failed', ['error' => $e->getMessage()]);
        }

        return redirect()->away($url);
    }

    private function isAllowedOutboundUrl(string $url): bool
    {
        if (! str_starts_with($url, 'https://')) {
            return false;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return false;
        }

        $blocked = ['localhost', '127.0.0.1', '0.0.0.0', '[::1]'];
        foreach ($blocked as $b) {
            if (strcasecmp($host, $b) === 0) {
                return false;
            }
        }

        return true;
    }
}
