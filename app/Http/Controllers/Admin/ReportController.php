<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function paidOrders($dateFrom = null, $dateTo = null): Builder
    {
        // Mỗi đơn chỉ tính một lần; ưu tiên giao dịch đã thu/hoàn tiền hơn lần thử mới.
        $paymentStatus = DB::table('payment_transactions')->select('status')
            ->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $query = Order::query()->where('orders.created_at', '<=', now())
            ->where('orders.status', '!=', 'cancelled')
            ->whereNotIn('orders.shipping_status', ['cancelled', 'return', 'returned'])
            ->where(function (Builder $query) use ($paymentStatus) {
                $query->where($paymentStatus, 'paid')
                    ->orWhere(function (Builder $legacy) {
                        $legacy->whereDoesntHave('paymentTransactions')
                            ->whereIn('orders.status', ['paid', 'cod_paid', 'paid_momo']);
                    });
            });

        if ($dateFrom) {
            $query->where('orders.created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $query->where('orders.created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        return $query;
    }

    private function categoryRevenue($dateFrom = null, $dateTo = null): Collection
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('order_items.order_id', $this->paidOrders($dateFrom, $dateTo)->select('orders.id'))
            ->select('products.category_id', 'categories.name as category_name')
            ->selectRaw('SUM(order_items.price * order_items.quantity) as total_revenue, SUM(order_items.quantity) as total_qty')
            ->groupBy('products.category_id', 'categories.name')
            ->orderByDesc('total_revenue')->get();
    }

    private function dailyRevenue($dateFrom = null, $dateTo = null): Collection
    {
        return $this->paidOrders($dateFrom, $dateTo)
            ->selectRaw('DATE(orders.created_at) as date, SUM(total_price) as total_revenue, COUNT(*) as order_count')
            ->groupByRaw('DATE(orders.created_at)')->orderBy('date')->get();
    }

    // Tổng hợp từ dữ liệu theo ngày, dùng được với cả MySQL và SQLite.
    private function periodRevenue(Collection $days, string $period): Collection
    {
        return $days->groupBy(fn ($day) => substr($day->date, 0, $period === 'month' ? 7 : 4))
            ->map(fn (Collection $rows, $key) => (object) [
                $period => (string) $key,
                'total_revenue' => $rows->sum('total_revenue'),
                'order_count' => $rows->sum('order_count'),
            ])->values();
    }

    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $categoryRevenue = $this->categoryRevenue($dateFrom, $dateTo);

        $orderQuery = Order::where('created_at', '<=', now());
        if ($dateFrom) {
            $orderQuery->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }
        if ($dateTo) {
            $orderQuery->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }
        $totalOrders = $orderQuery->count();

        $totalCustomers = DB::table('users')->where('role', '!=', 'admin')->count();
        $revenueByDate = $this->dailyRevenue($dateFrom, $dateTo);
        $revenueByMonth = $this->periodRevenue($revenueByDate, 'month');
        $revenueByYear = $this->periodRevenue($revenueByDate, 'year');
        $totalRevenue = $revenueByDate->sum('total_revenue');

        // Top 10 Sản phẩm bán chạy nhất
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('order_items.order_id', $this->paidOrders($dateFrom, $dateTo)->select('orders.id'))
            ->select('products.id', 'products.name', 'products.image')
            ->selectRaw('SUM(order_items.quantity) as total_sold, SUM(order_items.price * order_items.quantity) as total_revenue')
            ->groupBy('products.id', 'products.name', 'products.image')
            ->orderByDesc('total_sold')
            ->take(10)
            ->get();

        // Top 10 Khách hàng mua nhiều nhất
        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereIn('orders.id', $this->paidOrders($dateFrom, $dateTo)->select('orders.id'))
            ->select('users.id', 'users.name', 'users.email')
            ->selectRaw('COUNT(orders.id) as total_orders, SUM(orders.total_price) as total_spent')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'categoryRevenue', 'totalOrders', 'totalCustomers', 'totalRevenue',
            'revenueByDate', 'revenueByMonth', 'revenueByYear', 'dateFrom', 'dateTo',
            'topProducts', 'topCustomers'
        ));
    }

    public function charts(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $categories = $this->categoryRevenue($dateFrom, $dateTo);
        $catLabels = $categories->map(fn ($row) => $row->category_name ?? 'Danh mục #'.$row->category_id)->all();
        $catRevenue = $categories->pluck('total_revenue')->map(fn ($value) => (float) $value)->all();

        $daily = $this->dailyRevenue($dateFrom, $dateTo);
        $byDate = $daily->keyBy('date');
        $byMonth = $this->periodRevenue($daily, 'month')->keyBy('month');
        $byYear = $this->periodRevenue($daily, 'year');

        $startDay = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : Carbon::now()->startOfDay()->subDays(29);
        $endDay = $dateTo ? Carbon::parse($dateTo)->endOfDay() : Carbon::now()->endOfDay();
        $diffDays = min(max(1, $startDay->diffInDays($endDay) + 1), 60);

        $revDateLabels = $revDateData = [];
        for ($i = 0; $i < $diffDays; $i++) {
            $date = $startDay->copy()->addDays($i)->toDateString();
            $revDateLabels[] = $date;
            $revDateData[] = (float) ($byDate->get($date)?->total_revenue ?? 0);
        }

        $startMonth = Carbon::now()->startOfMonth()->subMonths(11);
        $revMonthLabels = $revMonthData = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $revMonthLabels[] = $month->format('m/Y');
            $revMonthData[] = (float) ($byMonth->get($month->format('Y-m'))?->total_revenue ?? 0);
        }

        $revYearLabels = $byYear->pluck('year')->all();
        $revYearData = $byYear->pluck('total_revenue')->map(fn ($value) => (float) $value)->all();

        $gateway = DB::table('payment_transactions')->select('gateway')
            ->whereColumn('order_id', 'orders.id')->where('status', 'paid')->orderByDesc('id')->limit(1);

        $paid = $this->paidOrders($dateFrom, $dateTo)->select('orders.total_price')->selectSub($gateway, 'gateway')
            ->selectRaw("CASE WHEN orders.status = 'cod_paid' THEN 'cod' ELSE 'momo' END as legacy_gateway");

        $methodRevenue = DB::query()->fromSub($paid, 'paid_orders')
            ->selectRaw('COALESCE(gateway, legacy_gateway) as method, SUM(total_price) as revenue')
            ->groupByRaw('COALESCE(gateway, legacy_gateway)')->pluck('revenue', 'method');

        $paymentMethodLabels = ['MoMo', 'COD'];
        $paymentMethodRevenue = [(float) $methodRevenue->get('momo', 0), (float) $methodRevenue->get('cod', 0)];

        return view('admin.reports.charts', compact(
            'catLabels', 'catRevenue', 'revDateLabels', 'revDateData',
            'revMonthLabels', 'revMonthData', 'revYearLabels', 'revYearData',
            'paymentMethodLabels', 'paymentMethodRevenue', 'dateFrom', 'dateTo'
        ));
    }
}
