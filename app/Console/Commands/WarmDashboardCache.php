<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheWarmingService;

class WarmDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:warm-dashboard {period=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-warm dashboard caches';

    /**
     * Execute the console command.
     */
    public function handle(CacheWarmingService $cacheService)
    {
        $period = $this->argument('period');

        $this->info('Warming dashboard caches for period '.$period);

        if ($period == 'all') {
            $results = $cacheService->warmAllDashboardCaches();
        } else {
            $results = $cacheService->warmDashboardCache($period);
        }

        $this->info('Cache warmed!');

        return Command::SUCCESS;
    }
}
