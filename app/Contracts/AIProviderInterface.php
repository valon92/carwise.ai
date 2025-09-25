<?php

namespace App\Contracts;

interface AIProviderInterface
{
    /**
     * Analyze vehicle diagnosis data and return AI response
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function analyzeDiagnosis(array $data): array;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * Check if provider is available
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Get estimated cost for this request
     *
     * @param array $data
     * @return float
     */
    public function getEstimatedCost(array $data): float;
}
