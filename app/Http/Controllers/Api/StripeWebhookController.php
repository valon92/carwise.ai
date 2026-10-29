<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeDiagnosisPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeDiagnosisPaymentService $payments): Response
    {
        try {
            $payments->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook failed', ['error' => $e->getMessage()]);

            return response($e->getMessage(), 400);
        }

        return response('ok', 200);
    }
}
