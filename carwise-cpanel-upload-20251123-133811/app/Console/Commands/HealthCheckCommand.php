<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Services\AIProviderManager;

class HealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive health check of the CarWise.ai system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏥 CarWise.ai Health Check Starting...');
        
        $checks = [
            'Database Connection' => $this->checkDatabase(),
            'Cache System' => $this->checkCache(),
            'Storage System' => $this->checkStorage(),
            'AI Providers' => $this->checkAIProviders(),
            'Environment' => $this->checkEnvironment(),
            'Dependencies' => $this->checkDependencies(),
        ];

        $this->newLine();
        $this->info('📊 Health Check Results:');
        $this->newLine();

        $allHealthy = true;
        foreach ($checks as $check => $result) {
            $status = $result ? '✅' : '❌';
            $this->line("{$status} {$check}");
            if (!$result) $allHealthy = false;
        }

        $this->newLine();
        if ($allHealthy) {
            $this->info('🎉 All systems are healthy! CarWise.ai is ready for production.');
            return 0;
        } else {
            $this->error('⚠️ Some systems need attention. Please check the issues above.');
            return 1;
        }
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->error("Database error: " . $e->getMessage());
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            // Use file cache for health check if Redis is not available
            $driver = config('cache.default');
            if ($driver === 'redis' && !extension_loaded('redis')) {
                config(['cache.default' => 'file']);
            }
            
            Cache::put('health_check', 'ok', 60);
            $result = Cache::get('health_check') === 'ok';
            
            // Restore original driver
            config(['cache.default' => $driver]);
            
            return $result;
        } catch (\Exception $e) {
            $this->error("Cache error: " . $e->getMessage());
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            Storage::disk('public')->put('health_check.txt', 'ok');
            $content = Storage::disk('public')->get('health_check.txt');
            Storage::disk('public')->delete('health_check.txt');
            return $content === 'ok';
        } catch (\Exception $e) {
            $this->error("Storage error: " . $e->getMessage());
            return false;
        }
    }

    private function checkAIProviders(): bool
    {
        try {
            $manager = new AIProviderManager();
            $info = $manager->getProviderInfo();
            return !empty($info['available_providers']) || $info['fallback_enabled'];
        } catch (\Exception $e) {
            $this->error("AI Providers error: " . $e->getMessage());
            return false;
        }
    }

    private function checkEnvironment(): bool
    {
        $required = ['APP_KEY', 'DB_CONNECTION'];
        foreach ($required as $key) {
            if (empty(env($key))) {
                $this->error("Missing environment variable: {$key}");
                return false;
            }
        }
        
        // Check optional but recommended variables
        $optional = ['CACHE_DRIVER', 'QUEUE_CONNECTION'];
        foreach ($optional as $key) {
            if (empty(env($key))) {
                $this->warn("Optional environment variable not set: {$key}");
            }
        }
        
        return true;
    }

    private function checkDependencies(): bool
    {
        try {
            // Check if required PHP extensions are loaded
            $required = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
            foreach ($required as $ext) {
                if (!extension_loaded($ext)) {
                    $this->error("Missing PHP extension: {$ext}");
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->error("Dependencies error: " . $e->getMessage());
            return false;
        }
    }
}
