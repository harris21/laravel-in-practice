<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\DailySalesReportMail;
use App\Services\SalesReportService;
use Illuminate\Support\Facades\Mail;

class GenerateDailySalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-sales-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(SalesReportService $reportService)
    {
        $report = $reportService->dashboardReport('today');

        Mail::to('test@test.com')->send(new DailySalesReportMail($report));
    }
}
