<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\SalesReportService;

class DashboardController extends Controller
{
    public function index(SalesReportService $reportService)
    {
        $report = $reportService->dashboardReport('month');

        return view('dashboard', compact('report'));
    }
}
