<?php

namespace App\Services;

class DiagnosisTerminology
{
    /**
     * Automotive abbreviations expanded for non-technical owners.
     * First occurrence can be rewritten as "EGR (Exhaust Gas Recirculation / riciklimi i gazrave të shkarkimit)".
     */
    public static function glossary(?string $language = 'en'): array
    {
        $sq = str_starts_with(strtolower((string) $language), 'sq');

        return [
            'DTC' => $sq
                ? 'DTC (Diagnostic Trouble Code / kodi i gabimit të diagnostikimit)'
                : 'DTC (Diagnostic Trouble Code)',
            'OBD' => $sq
                ? 'OBD (On-Board Diagnostics / diagnostikimi i bordit)'
                : 'OBD (On-Board Diagnostics)',
            'OBD-II' => $sq
                ? 'OBD-II (On-Board Diagnostics II / diagnostikimi standard i bordit)'
                : 'OBD-II (On-Board Diagnostics II)',
            'VCDS' => $sq
                ? 'VCDS (VAG-COM Diagnostic System / skaner për Volkswagen Group)'
                : 'VCDS (VAG-COM Diagnostic System for Volkswagen Group)',
            'EGR' => $sq
                ? 'EGR (Exhaust Gas Recirculation / riciklimi i gazrave të shkarkimit)'
                : 'EGR (Exhaust Gas Recirculation)',
            'DPF' => $sq
                ? 'DPF (Diesel Particulate Filter / filtri i grimcave të naftës)'
                : 'DPF (Diesel Particulate Filter)',
            'MAF' => $sq
                ? 'MAF (Mass Air Flow sensor / sensori i masës së ajrit)'
                : 'MAF (Mass Air Flow sensor)',
            'ECU' => $sq
                ? 'ECU (Engine Control Unit / kompjuteri i motorit)'
                : 'ECU (Engine Control Unit)',
            'CEL' => $sq
                ? 'CEL (Check Engine Light / drita e motorit)'
                : 'CEL (Check Engine Light)',
            'TDI' => $sq
                ? 'TDI (Turbocharged Direct Injection / dizel me injeksion të drejtpërdrejtë)'
                : 'TDI (Turbocharged Direct Injection diesel)',
            'OEM' => $sq
                ? 'OEM (Original Equipment Manufacturer / pjesë origjinale fabrike)'
                : 'OEM (Original Equipment Manufacturer)',
        ];
    }

    public static function expandText(?string $text, ?string $language = 'en'): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $glossary = self::glossary($language);
        // Longer keys first (OBD-II before OBD)
        uksort($glossary, fn ($a, $b) => strlen($b) <=> strlen($a));

        $seen = [];
        foreach ($glossary as $abbr => $expanded) {
            $pattern = '/\b'.preg_quote($abbr, '/').'\b/u';
            $text = preg_replace_callback($pattern, function ($m) use ($abbr, $expanded, &$seen) {
                if (isset($seen[$abbr])) {
                    return $m[0];
                }
                $seen[$abbr] = true;

                return $expanded;
            }, $text, 1) ?? $text;
        }

        return $text;
    }

    public static function expandResult(array $result, ?string $language = 'en'): array
    {
        foreach (['problem_title', 'problem_description', 'hypothesis_disclaimer'] as $key) {
            if (! empty($result[$key]) && is_string($result[$key])) {
                $result[$key] = self::expandText($result[$key], $language);
            }
        }

        foreach (['likely_causes', 'recommended_actions'] as $listKey) {
            if (empty($result[$listKey]) || ! is_array($result[$listKey])) {
                continue;
            }
            foreach ($result[$listKey] as &$row) {
                if (! is_array($row)) {
                    continue;
                }
                foreach (['title', 'description', 'part_name', 'action', 'cause', 'explanation'] as $field) {
                    if (! empty($row[$field]) && is_string($row[$field])) {
                        $row[$field] = self::expandText($row[$field], $language);
                    }
                }
            }
            unset($row);
        }

        foreach (['ai_insights', 'related_issues', 'next_workshop_questions'] as $listKey) {
            if (empty($result[$listKey]) || ! is_array($result[$listKey])) {
                continue;
            }
            $result[$listKey] = array_map(
                fn ($item) => is_string($item) ? self::expandText($item, $language) : $item,
                $result[$listKey]
            );
        }

        if (! empty($result['diagnostic_codes_to_check']) && is_array($result['diagnostic_codes_to_check'])) {
            foreach ($result['diagnostic_codes_to_check'] as &$code) {
                if (! is_array($code)) {
                    continue;
                }
                foreach (['meaning', 'related_cause'] as $field) {
                    if (! empty($code[$field]) && is_string($code[$field])) {
                        $code[$field] = self::expandText($code[$field], $language);
                    }
                }
            }
            unset($code);
        }

        if (! empty($result['estimated_costs']) && is_array($result['estimated_costs'])) {
            foreach ($result['estimated_costs'] as &$cost) {
                if (is_array($cost) && ! empty($cost['service']) && is_string($cost['service'])) {
                    $cost['service'] = self::expandText($cost['service'], $language);
                }
            }
            unset($cost);
        }

        $result['glossary'] = self::glossary($language);

        return $result;
    }
}
