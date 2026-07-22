<?php

namespace App\DiagnosticEcosystem\Providers;

use App\DiagnosticEcosystem\Adapters\AiDiagnosisAdapter;
use App\DiagnosticEcosystem\Adapters\CarApiVinAdapter;
use App\DiagnosticEcosystem\Adapters\ManufacturerVinAdapter;
use App\DiagnosticEcosystem\Adapters\NhtsaVinAdapter;
use App\DiagnosticEcosystem\Adapters\PartsMarketplaceAdapter;
use App\DiagnosticEcosystem\Contracts\DiagnosticAnalysisProviderInterface;
use App\DiagnosticEcosystem\Contracts\MarketplaceIntegrationInterface;
use App\DiagnosticEcosystem\Contracts\PredictiveMaintenanceInterface;
use App\DiagnosticEcosystem\Contracts\SmartConnectorInterface;
use App\DiagnosticEcosystem\Contracts\VehicleHistoryStoreInterface;
use App\DiagnosticEcosystem\Services\PredictiveMaintenanceService;
use App\DiagnosticEcosystem\Services\VehicleHistoryService;
use App\DiagnosticEcosystem\Stubs\NullSmartConnector;
use Illuminate\Support\ServiceProvider;

class DiagnosticEcosystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../../config/diagnostic-ecosystem.php',
            'diagnostic-ecosystem'
        );

        $this->app->bind(SmartConnectorInterface::class, NullSmartConnector::class);
        $this->app->bind(DiagnosticAnalysisProviderInterface::class, AiDiagnosisAdapter::class);
        $this->app->bind(VehicleHistoryStoreInterface::class, VehicleHistoryService::class);
        $this->app->bind(PredictiveMaintenanceInterface::class, PredictiveMaintenanceService::class);
        $this->app->bind(MarketplaceIntegrationInterface::class, PartsMarketplaceAdapter::class);

        $this->app->singleton(NhtsaVinAdapter::class);
        $this->app->singleton(CarApiVinAdapter::class);
        $this->app->singleton(ManufacturerVinAdapter::class);
        $this->app->singleton(AiDiagnosisAdapter::class);
        $this->app->singleton(PartsMarketplaceAdapter::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/diagnostic-ecosystem.php'));
        $this->loadViewsFrom(resource_path('views/diagnostic-ecosystem'), 'diagnostic-ecosystem');
    }
}
