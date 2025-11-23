<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Get all active currencies
     */
    public function index()
    {
        $currencies = Currency::getActive();
        
        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Get default currency
     */
    public function default()
    {
        $defaultCurrency = Currency::getDefault();
        
        return response()->json([
            'success' => true,
            'data' => $defaultCurrency
        ]);
    }

    /**
     * Convert amount between currencies
     */
    public function convert(Request $request)
    {
        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id',
            'amount' => 'required|numeric|min:0'
        ]);

        $fromCurrency = Currency::findOrFail($request->from_currency_id);
        $toCurrency = Currency::findOrFail($request->to_currency_id);
        $amount = $request->amount;

        $convertedAmount = $fromCurrency->convertTo($toCurrency, $amount);

        return response()->json([
            'success' => true,
            'data' => [
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'original_amount' => $amount,
                'converted_amount' => $convertedAmount,
                'formatted_original' => $fromCurrency->format($amount),
                'formatted_converted' => $toCurrency->format($convertedAmount)
            ]
        ]);
    }

    /**
     * Get currency by code
     */
    public function show($code)
    {
        $currency = Currency::where('code', strtoupper($code))->first();
        
        if (!$currency) {
            return response()->json([
                'success' => false,
                'message' => 'Currency not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $currency
        ]);
    }

    /**
     * Get popular currencies (major world currencies)
     */
    public function popular()
    {
        $popularCodes = ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF'];
        
        $currencies = Currency::whereIn('code', $popularCodes)
            ->where('is_active', true)
            ->orderByRaw("FIELD(code, '" . implode("','", $popularCodes) . "')")
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Search currencies
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json([
                'success' => true,
                'data' => Currency::getActive()
            ]);
        }

        $currencies = Currency::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('code', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%")
                  ->orWhere('country', 'like', "%{$query}%");
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }
}