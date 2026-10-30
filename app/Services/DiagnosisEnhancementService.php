<?php

namespace App\Services;

use App\Models\CarPart;
use Illuminate\Support\Facades\Log;

class DiagnosisEnhancementService
{
    /**
     * Light post-processing only — never invent fake videos or stock "repair" media.
     */
    public function enhanceDiagnosisResults(array $aiResult, string $make, string $model, int $year): array
    {
        try {
            if (isset($aiResult['likely_causes']) && is_array($aiResult['likely_causes'])) {
                $aiResult['likely_causes'] = $this->enhanceLikelyCauses(
                    $aiResult['likely_causes'],
                    $make,
                    $model,
                    $year
                );
            }

            if (isset($aiResult['recommended_actions']) && is_array($aiResult['recommended_actions'])) {
                $aiResult['recommended_actions'] = $this->sanitizeActions($aiResult['recommended_actions']);
            }

            if (isset($aiResult['estimated_costs']) && is_array($aiResult['estimated_costs'])) {
                $aiResult['estimated_costs'] = $this->normalizeCosts($aiResult['estimated_costs']);
            }

            // Prefer real catalogue matches only; never fabricate marketplace rows.
            $aiResult['suggested_parts_for_purchase'] = $this->generateSuggestedParts(
                $aiResult,
                $make,
                $model,
                $year
            );

            // No invented repair video catalogue.
            $aiResult['repair_videos'] = [];

            return $aiResult;
        } catch (\Exception $e) {
            Log::error('Error enhancing diagnosis results: ' . $e->getMessage());

            return $aiResult;
        }
    }

    private function enhanceLikelyCauses(array $causes, string $make, string $model, int $year): array
    {
        foreach ($causes as &$cause) {
            if (!is_array($cause)) {
                continue;
            }

            // Do not attach Unsplash placeholders as if they were the failed part.
            $cause['part_image_url'] = '';

            if (!empty($cause['part_name'])) {
                $cause['car_parts_url'] = $this->generateCarPartsUrl(
                    (string) $cause['part_name'],
                    (string) ($cause['part_category'] ?? '')
                );
                $dbParts = $this->getSuggestedParts((string) $cause['part_name'], $make, $model, $year);
                if ($dbParts !== []) {
                    $cause['suggested_parts'] = $dbParts;
                } else {
                    $cause['suggested_parts'] = [];
                }
            }
        }
        unset($cause);

        return array_values(array_filter($causes, 'is_array'));
    }

    private function sanitizeActions(array $actions): array
    {
        $blockedHosts = ['youtube.com', 'youtu.be', 'unsplash.com'];

        foreach ($actions as &$action) {
            if (!is_array($action)) {
                continue;
            }

            $url = (string) ($action['video_url'] ?? '');
            $lower = strtolower($url);
            $isBlocked = $url === '' || str_contains($lower, 'dqw4w9wgxcq');
            foreach ($blockedHosts as $host) {
                if (str_contains($lower, $host)) {
                    $isBlocked = true;
                    break;
                }
            }

            if ($isBlocked) {
                $action['video_url'] = '';
                $action['video_title'] = '';
                $action['video_duration'] = '';
                $action['video_company'] = '';
            }

            if (empty($action['tools_needed']) || !is_array($action['tools_needed'])) {
                $action['tools_needed'] = ['OBD-II / VCDS scanner', 'Basic hand tools'];
            }
        }
        unset($action);

        return array_values(array_filter($actions, 'is_array'));
    }

    private function normalizeCosts(array $costs): array
    {
        foreach ($costs as &$cost) {
            if (!is_array($cost)) {
                continue;
            }
            $cost['currency'] = 'EUR';
            if (isset($cost['min'])) {
                $cost['formatted_min'] = 'EUR ' . $cost['min'];
            }
            if (isset($cost['max'])) {
                $cost['formatted_max'] = 'EUR ' . $cost['max'];
            }
        }
        unset($cost);

        return array_values(array_filter($costs, 'is_array'));
    }

    private function generateSuggestedParts(array $aiResult, string $make, string $model, int $year): array
    {
        $suggestedParts = [];

        if (!isset($aiResult['likely_causes']) || !is_array($aiResult['likely_causes'])) {
            return [];
        }

        foreach ($aiResult['likely_causes'] as $cause) {
            if (!is_array($cause) || empty($cause['part_name'])) {
                continue;
            }
            $parts = $this->getSuggestedParts((string) $cause['part_name'], $make, $model, $year);
            $suggestedParts = array_merge($suggestedParts, $parts);
        }

        return array_values(array_unique($suggestedParts, SORT_REGULAR));
    }

    private function generateCarPartsUrl(string $partName, string $category = ''): string
    {
        $search = urlencode($partName);
        $categoryParam = $category !== '' ? '&category=' . urlencode($category) : '';

        return "/car-parts?search={$search}{$categoryParam}";
    }

    private function getSuggestedParts(string $partName, string $make, string $model, int $year): array
    {
        try {
            $parts = CarPart::query()
                ->where(function ($q) use ($partName) {
                    $q->where('name', 'LIKE', "%{$partName}%")
                        ->orWhere('description', 'LIKE', "%{$partName}%");
                })
                ->limit(3)
                ->get();

            $suggestedParts = [];
            foreach ($parts as $part) {
                $suggestedParts[] = [
                    'name' => $part->name,
                    'brand' => $part->brand,
                    'part_number' => $part->part_number,
                    'price_range' => $this->formatPriceRange((float) $part->price),
                    'compatibility' => "Compatible with {$make} {$model} {$year}",
                    'image_url' => $part->image_url ?? '',
                    'description' => $part->description,
                    'warranty' => $part->warranty ?? '1 year warranty',
                    'availability' => ($part->stock_quantity ?? 0) > 0 ? 'In stock' : 'Out of stock',
                    'shipping_time' => '2-5 business days',
                    'car_parts_url' => $this->generateCarPartsUrl($part->name, (string) ($part->category ?? '')),
                ];
            }

            return $suggestedParts;
        } catch (\Exception $e) {
            Log::error('Error getting suggested parts: ' . $e->getMessage());

            return [];
        }
    }

    private function formatPriceRange(float $price): string
    {
        $min = $price * 0.8;
        $max = $price * 1.2;

        return 'EUR ' . number_format($min, 2) . ' - ' . number_format($max, 2);
    }
}
