<?php

namespace App\Repositories\Dashboard;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HomeDashboardRepository
{
    public function getKpis(Carbon $start, Carbon $end, array $salesStatuses): array
    {
        $pipelineStatuses = array_values(array_diff($salesStatuses, [Order::STATUS_DELIVERED]));

        return [
            'users_total' => User::query()->count(),
            'active_users' => User::query()->where('status', true)->count(),
            'new_users_in_range' => User::query()->whereBetween('created_at', [$start, $end])->count(),
            'orders_total' => Order::query()->count(),
            'orders_in_range' => $this->ordersInRangeQuery($start, $end)->count(),
            'sales_orders_in_range' => $this->ordersInRangeQuery($start, $end)
                ->whereIn('status', $salesStatuses)
                ->count(),
            'delivered_orders_total' => Order::query()->where('status', Order::STATUS_DELIVERED)->count(),
            'delivered_orders_in_range' => $this->ordersInRangeQuery($start, $end)
                ->where('status', Order::STATUS_DELIVERED)
                ->count(),
            'canceled_orders_total' => Order::query()->where('status', Order::STATUS_CANCELED)->count(),
            'sales_revenue_total' => (float) Order::query()->whereIn('status', $salesStatuses)->sum('total'),
            'sales_revenue_in_range' => (float) $this->ordersInRangeQuery($start, $end)
                ->whereIn('status', $salesStatuses)
                ->sum('total'),
            'realized_revenue_total' => (float) Order::query()->where('status', Order::STATUS_DELIVERED)->sum('total'),
            'realized_revenue_in_range' => (float) $this->ordersInRangeQuery($start, $end)
                ->where('status', Order::STATUS_DELIVERED)
                ->sum('total'),
            'pipeline_revenue_total' => (float) Order::query()->whereIn('status', $pipelineStatuses)->sum('total'),
            'pipeline_revenue_in_range' => (float) $this->ordersInRangeQuery($start, $end)
                ->whereIn('status', $pipelineStatuses)
                ->sum('total'),
            'products_total' => Product::query()->count(),
            'active_products' => Product::query()->where('status', true)->count(),
            'categories_total' => Category::query()->count(),
            'active_categories' => Category::query()->where('status', true)->count(),
            'wallet_balance_total' => (float) Wallet::query()->sum('balance'),
            'active_wallets' => Wallet::query()->where('status', true)->count(),
            'favorites_total' => Favorite::query()->count(),
            'favorites_in_range' => Favorite::query()->whereBetween('created_at', [$start, $end])->count(),
            'contacts_total' => Contact::query()->count(),
            'contacts_in_range' => Contact::query()->whereBetween('created_at', [$start, $end])->count(),
        ];
    }

    public function getOrdersTrend(Carbon $start, Carbon $end): Collection
    {
        return $this->ordersInRangeQuery($start, $end)
            ->selectRaw('DATE(' . $this->orderDateExpression() . ') as date_key')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->pluck('aggregate', 'date_key');
    }

    public function getRevenueTrend(Carbon $start, Carbon $end, array $salesStatuses): Collection
    {
        return $this->ordersInRangeQuery($start, $end)
            ->whereIn('status', $salesStatuses)
            ->selectRaw('DATE(' . $this->orderDateExpression() . ') as date_key')
            ->selectRaw('COALESCE(SUM(total), 0) as aggregate')
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->pluck('aggregate', 'date_key');
    }

    public function getOrderStatusDistribution(Carbon $start, Carbon $end): Collection
    {
        return $this->ordersInRangeQuery($start, $end)
            ->select('status')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
    }

    public function getPaymentMethodDistribution(Carbon $start, Carbon $end): Collection
    {
        return $this->ordersInRangeQuery($start, $end)
            ->select('payment_method')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('payment_method')
            ->pluck('aggregate', 'payment_method');
    }

    public function getTopProducts(Carbon $start, Carbon $end, array $salesStatuses, int $limit = 5): Collection
    {
        $rows = OrderItem::query()
            ->select('order_items.product_id', 'order_items.product_name')
            ->selectRaw('MIN(order_items.product_image) as product_image')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('COALESCE(SUM(order_items.line_total), 0) as sales_total')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $salesStatuses)
            ->whereBetween(DB::raw($this->orderDateExpression()), [$start->toDateTimeString(), $end->toDateTimeString()])
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('units_sold')
            ->orderByDesc('sales_total')
            ->limit($limit)
            ->get();

        $products = Product::query()
            ->with([
                'category:id,name',
                'images:id,product_id,image,sort_order',
            ])
            ->whereIn('id', $rows->pluck('product_id')->filter()->all())
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($products) {
            $product = $row->product_id ? $products->get((int) $row->product_id) : null;

            return (object) [
                'product' => $product,
                'product_id' => $row->product_id ? (int) $row->product_id : null,
                'name' => $product?->name ?? $row->product_name,
                'category_name' => $product?->category?->name,
                'image' => $product?->image ?? $row->product_image,
                'units_sold' => (int) $row->units_sold,
                'sales_total' => (float) $row->sales_total,
                'current_price' => $product ? $product->priceAfterDiscount() : null,
                'sku' => $product?->sku,
            ];
        });
    }

    public function getTopCategories(Carbon $start, Carbon $end, array $salesStatuses, int $limit = 5): Collection
    {
        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('orders.status', $salesStatuses)
            ->whereBetween(DB::raw($this->orderDateExpression()), [$start->toDateTimeString(), $end->toDateTimeString()])
            ->select('categories.id')
            ->selectRaw('SUM(order_items.quantity) as units_sold')
            ->selectRaw('COALESCE(SUM(order_items.line_total), 0) as sales_total')
            ->selectRaw('COUNT(DISTINCT products.id) as products_count')
            ->groupBy('categories.id')
            ->orderByDesc('units_sold')
            ->orderByDesc('sales_total')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return Category::query()
                ->withCount('products')
                ->orderByDesc('products_count')
                ->limit($limit)
                ->get()
                ->map(fn (Category $category) => (object) [
                    'category' => $category,
                    'name' => $category->name,
                    'units_sold' => 0,
                    'sales_total' => 0.0,
                    'products_count' => (int) $category->products_count,
                ]);
        }

        $categories = Category::query()
            ->whereIn('id', $rows->pluck('id')->all())
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($categories) {
            $category = $categories->get((int) $row->id);

            return (object) [
                'category' => $category,
                'name' => $category?->name ?? '-',
                'units_sold' => (int) $row->units_sold,
                'sales_total' => (float) $row->sales_total,
                'products_count' => (int) $row->products_count,
            ];
        });
    }

    public function getRecentOrders(int $limit = 6): Collection
    {
        return Order::query()
            ->with([
                'user:id,name,image',
            ])
            ->withCount('items')
            ->orderByRaw($this->orderDateExpression() . ' desc')
            ->latest('id')
            ->limit($limit)
            ->get([
                'id',
                'user_id',
                'order_number',
                'status',
                'payment_method',
                'payment_status',
                'total',
                'placed_at',
                'created_at',
            ]);
    }

    public function getRecentUsers(int $limit = 6): Collection
    {
        return User::query()
            ->latest('created_at')
            ->limit($limit)
            ->get([
                'id',
                'name',
                'email',
                'phone',
                'image',
                'created_at',
            ]);
    }

    public function getRecentWalletTransactions(int $limit = 8): Collection
    {
        return WalletTransaction::query()
            ->with([
                'user:id,name,image',
                'order:id,order_number',
            ])
            ->latest('id')
            ->limit($limit)
            ->get([
                'id',
                'user_id',
                'order_id',
                'type',
                'transaction_type',
                'amount',
                'reference',
                'status',
                'created_at',
            ]);
    }

    private function ordersInRangeQuery(Carbon $start, Carbon $end)
    {
        return Order::query()
            ->whereBetween(DB::raw($this->orderDateExpression()), [$start->toDateTimeString(), $end->toDateTimeString()]);
    }

    private function orderDateExpression(string $table = 'orders'): string
    {
        return 'COALESCE(' . $table . '.placed_at, ' . $table . '.created_at)';
    }
}
