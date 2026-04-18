<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Get cart count
        $cart = Cart::getOrCreateForUser($userId);
        $cartCount = $cart->getTotalItems();

        // Get order statistics
        $pendingPaymentCount = Order::byUser($userId)
            ->where('status', Order::STATUS_PENDING)
            ->count();

        $processingCount = Order::byUser($userId)
            ->where('status', Order::STATUS_PROCESSING)
            ->count();

        // Get total spent this month from completed orders
        $totalSpentThisMonth = Order::byUser($userId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Get pending payment total amount (for display in the card)
        $pendingPaymentTotal = Order::byUser($userId)
            ->where('status', Order::STATUS_PENDING)
            ->sum('total_amount');

        // Get recent orders (last 3)
        $recentOrders = Order::byUser($userId)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Calculate weekly purchase data for chart (last 6 weeks)
        $weeklyPurchases = [];
        $weekLabels = [];
        $now = Carbon::now();

        for ($i = 5; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $now->copy()->subWeeks($i)->endOfWeek();

            $weekTotal = Order::byUser($userId)
                ->where('status', Order::STATUS_COMPLETED)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_amount');

            $weeklyPurchases[] = (float) $weekTotal;
            $weekLabels[] = 'Minggu '.($i + 1);
        }

        // Calculate growth percentage (current month vs previous month)
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentMonthTotal = Order::byUser($userId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->sum('total_amount');

        $previousMonthTotal = Order::byUser($userId)
            ->where('status', Order::STATUS_COMPLETED)
            ->whereMonth('created_at', $previousMonth->month)
            ->whereYear('created_at', $previousMonth->year)
            ->sum('total_amount');

        if ($previousMonthTotal > 0) {
            $growthPercentage = round((($currentMonthTotal - $previousMonthTotal) / $previousMonthTotal) * 100, 1);
        } else {
            $growthPercentage = $currentMonthTotal > 0 ? 100 : 0;
        }

        // Get recommended products (4 random active products with images)
        $recommendedProducts = Product::active()
            ->inStock()
            ->whereNotNull('images')
            ->orWhereNotNull('featured_image')
            ->inRandomOrder()
            ->take(4)
            ->get();

        // If we don't have enough products with images, get any active products
        if ($recommendedProducts->count() < 4) {
            $additionalProducts = Product::active()
                ->inStock()
                ->whereNotIn('id', $recommendedProducts->pluck('id'))
                ->inRandomOrder()
                ->take(4 - $recommendedProducts->count())
                ->get();
            $recommendedProducts = $recommendedProducts->merge($additionalProducts);
        }

        // Get categories with product counts for display
        $categories = Category::where('is_active', true)
            ->whereHas('activeProducts')
            ->withCount('activeProducts')
            ->take(8)
            ->get();

        // Get best sellers (most viewed products)
        $bestSellers = Product::active()
            ->inStock()
            ->orderBy('view_count', 'desc')
            ->take(10)
            ->get();

        // Get new arrivals (latest products)
        $newArrivals = Product::active()
            ->inStock()
            ->latest()
            ->take(10)
            ->get();

        return view('user.dashboard', compact(
            'cartCount',
            'pendingPaymentCount',
            'processingCount',
            'totalSpentThisMonth',
            'pendingPaymentTotal',
            'recentOrders',
            'recommendedProducts',
            'weeklyPurchases',
            'weekLabels',
            'growthPercentage',
            'categories',
            'bestSellers',
            'newArrivals'
        ));
    }
}
