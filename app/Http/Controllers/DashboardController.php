<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\SalesReportService;
use App\Services\FlexibleCacheService;
use App\Services\OptimizedSalesReportService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $service = match ($request->get('mode', 'cached')) {
            'original' => app(SalesReportService::class),
            'optimized' => app(OptimizedSalesReportService::class),
            'cached' => app(FlexibleCacheService::class),
        };

        $report = $service->dashboardReport('month');

        return view('dashboard', compact('report'));
    }
}
