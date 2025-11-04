<?php

use App\Models\Order;
use App\Services\SalesReportService;
use App\Services\FlexibleCacheService;
use App\Services\MemoizedCacheService;
use App\Services\OptimizedSalesReportService;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/reports', [ReportsController::class, 'index'])
    ->middleware(['auth'])
    ->name('reports');

Route::get('/benchmark', function () {
    echo "Testing with " . Order::count() . " orders\n";
    echo "Hardware: M4 Mac Mini, Local SQLite\n\n";

    Benchmark::dd([
        'Complete Dashboard' => fn() =>
        app(SalesReportService::class)->dashboardReport('month'),

        'Just Top Customers' => fn() =>
        Order::completed()->forPeriod('month')->get()->topCustomers(),

        'Just Business Summary' => fn() =>
        Order::completed()->forPeriod('month')->get()->businessSummary(),

        'Just Daily Breakdown' => fn() =>
        Order::completed()->forPeriod('month')->get()->dailyBreakdown(),
    ], iterations: 5);
});

Route::get('/explain', function () {
    $query = Order::where('status', 'completed')
        ->where('created_at', '>=', now()->subMonth());

    $sql = $query->toSql();
    $bindings = $query->getBindings();

    $explainSql = 'EXPLAIN QUERY PLAN ' . $sql;
    $result = DB::select($explainSql, $bindings);

    return response()->json([
        'query' => $sql,
        'bindings' => $bindings,
        'explain' => $result
    ]);
});

Route::get('/test-invalidation', function() {
    $service = app(MemoizedCacheService::class);
    $before = $service->dashboardReport('month');

    Order::factory()->completed()->create([
        'total' => 1000,
        'created_at' => now()
    ]);

    $after = $service->dashboardReport('month');

    return [
        'before_total' => $before['summary']['total_revenue'],
        'after_total' => $after['summary']['total_revenue'],
        'increased_by' => $after['summary']['total_revenue'] - $before['summary']['total_revenue'],
        'message' => 'Check Telescope to see ALL cache layers being cleared!'
    ];
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
