<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardExportController extends Controller
{
    /**
     * Export dashboard data as PDF
     */
    public function exportPDF(Request $request)
    {
        // Get dashboard statistics
        $totalProducts = Product::where('is_active', true)->count();
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $totalRevenue = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');
        
        // Get recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Create PDF content as HTML for now (can be converted to actual PDF later)
        $html = view('admin.exports.dashboard-pdf', compact(
            'totalProducts',
            'ordersThisMonth', 
            'totalRevenue',
            'recentOrders'
        ))->render();
        
        // For now, return as HTML download
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="dashboard-report-' . now()->format('Y-m-d') . '.html"'
        ]);
    }
}
