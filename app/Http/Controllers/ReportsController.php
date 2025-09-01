<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\SalesReportService;

class ReportsController extends Controller
{
    public function index(Request $request, SalesReportService $reportService)
    {
        $period = $request->input('period', 'month');

        return response()->json($reportService->dashboardReport($period));
    }
}
