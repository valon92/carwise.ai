<?php

namespace App\DiagnosticEcosystem\Contracts;

/**
 * Future marketplace integrations (Step 8).
 *
 * Parts, repair shops, dealers, insurance, roadside, inspection.
 */
interface MarketplaceIntegrationInterface
{
    public function searchParts(int $vehicleProfileId, string $query): array;

    public function findShops(int $vehicleProfileId, array $location): array;

    public function findDealers(int $vehicleProfileId): array;

    public function getInsuranceQuotes(int $vehicleProfileId): array;

    public function requestRoadside(int $vehicleProfileId): array;

    public function bookInspection(int $vehicleProfileId): array;
}
