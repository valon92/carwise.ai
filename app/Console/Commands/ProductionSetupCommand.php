<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ProductionSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:setup {--force : Force setup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup CarWise.ai for production deployment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 CarWise.ai Production Setup Starting...');
        $this->newLine();

        if (!$this->option('force') && !$this->confirm('This will optimize the application for production. Continue?')) {
            $this->info('Setup cancelled.');
            return 0;
        }

        $steps = [
            'Generate Application Key' => fn() => $this->generateAppKey(),
            'Run Database Migrations' => fn() => $this->runMigrations(),
            'Optimize Configuration' => fn() => $this->optimizeConfig(),
            'Build Frontend Assets' => fn() => $this->buildAssets(),
            'Create Storage Link' => fn() => $this->createStorageLink(),
            'Run Health Check' => fn() => $this->runHealthCheck(),
        ];

        foreach ($steps as $step => $callback) {
            $this->info("⏳ {$step}...");
            
            try {
                $callback();
                $this->info("✅ {$step} completed");
            } catch (\Exception $e) {
                $this->error("❌ {$step} failed: " . $e->getMessage());
                return 1;
            }
            
            $this->newLine();
        }

        $this->info('🎉 CarWise.ai is ready for production!');
        $this->newLine();
        $this->info('Next steps:');
        $this->line('1. Configure your web server (Nginx/Apache)');
        $this->line('2. Set up SSL certificate');
        $this->line('3. Configure environment variables');
        $this->line('4. Set up monitoring and logging');
        $this->line('5. Configure AI API keys');
        $this->line('6. Test all functionality');
        
        return 0;
    }

    private function generateAppKey(): void
    {
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
            $this->line('Application key generated');
        } else {
            $this->line('Application key already exists');
        }
    }

    private function runMigrations(): void
    {
        Artisan::call('migrate', ['--force' => true]);
        $this->line('Database migrations completed');
    }

    private function optimizeConfig(): void
    {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        $this->line('Configuration optimized');
    }

    private function buildAssets(): void
    {
        $this->line('Building frontend assets...');
        exec('npm run build', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Failed to build frontend assets');
        }
        
        $this->line('Frontend assets built successfully');
    }

    private function createStorageLink(): void
    {
        try {
            Artisan::call('storage:link');
            $this->line('Storage link created');
        } catch (\Exception $e) {
            $this->line('Storage link already exists or failed to create');
        }
    }

    private function runHealthCheck(): void
    {
        $exitCode = Artisan::call('health:check');
        
        if ($exitCode !== 0) {
            throw new \Exception('Health check failed');
        }
        
        $this->line('Health check passed');
    }
}
