<?php

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Repositories\Dashboard\HomeDashboardRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HomeDashboardService
{
    public function __construct(protected HomeDashboardRepository $homeDashboardRepository)
    {
    }

    public function build(string $range = '30d'): array
    {
        $filter = $this->resolveRange($range);
        $salesStatuses = $this->revenueEligibleStatuses();
        $kpis = $this->homeDashboardRepository->getKpis($filter['start'], $filter['end'], $salesStatuses);
        $topProducts = $this->homeDashboardRepository->getTopProducts($filter['start'], $filter['end'], $salesStatuses, 6);
        $topCategories = $this->homeDashboardRepository->getTopCategories($filter['start'], $filter['end'], $salesStatuses, 5);

        $ordersTrend = $this->buildDailySeries(
            $filter['start'],
            $filter['end'],
            $this->homeDashboardRepository->getOrdersTrend($filter['start'], $filter['end'])
        );

        $revenueTrend = $this->buildDailySeries(
            $filter['start'],
            $filter['end'],
            $this->homeDashboardRepository->getRevenueTrend($filter['start'], $filter['end'], $salesStatuses),
            2
        );

        $statusDistribution = $this->buildStatusDistribution(
            $this->homeDashboardRepository->getOrderStatusDistribution($filter['start'], $filter['end'])
        );

        $paymentMethodDistribution = $this->buildPaymentMethodDistribution(
            $this->homeDashboardRepository->getPaymentMethodDistribution($filter['start'], $filter['end'])
        );

        return [
            'filters' => [
                'active' => $filter['key'],
                'label' => __('dashboard.' . $filter['label_key']),
                'options' => [
                    '7d' => __('dashboard.last-7-days'),
                    '30d' => __('dashboard.last-30-days'),
                    '90d' => __('dashboard.last-90-days'),
                    'this_month' => __('dashboard.this-month'),
                ],
            ],
            'kpis' => $kpis,
            'hero' => [
                'orders_in_range' => $kpis['orders_in_range'],
                'sales_orders_in_range' => $kpis['sales_orders_in_range'],
                'sales_revenue_in_range' => $kpis['sales_revenue_in_range'],
                'new_users_in_range' => $kpis['new_users_in_range'],
                'contacts_in_range' => $kpis['contacts_in_range'],
                'average_order_value_in_range' => $kpis['sales_orders_in_range'] > 0
                    ? round($kpis['sales_revenue_in_range'] / $kpis['sales_orders_in_range'], 2)
                    : 0.0,
                'delivered_rate_in_range' => $kpis['orders_in_range'] > 0
                    ? round(($kpis['delivered_orders_in_range'] / $kpis['orders_in_range']) * 100, 1)
                    : 0.0,
            ],
            'charts' => [
                'ordersTrend' => $ordersTrend,
                'revenueTrend' => $revenueTrend,
                'statusDistribution' => $statusDistribution,
                'paymentMethodDistribution' => $paymentMethodDistribution,
                'topProductsChart' => [
                    'labels' => $topProducts->map(fn ($item) => $item->name)->values()->all(),
                    'series' => $topProducts->map(fn ($item) => $item->units_sold)->values()->all(),
                ],
            ],
            'sections' => [
                'recentOrders' => $this->homeDashboardRepository->getRecentOrders(),
                'topProducts' => $topProducts,
                'topCategories' => $topCategories,
                'recentUsers' => $this->homeDashboardRepository->getRecentUsers(),
                'recentWalletTransactions' => $this->homeDashboardRepository->getRecentWalletTransactions(),
            ],
        ];
    }

    public function revenueEligibleStatuses(): array
    {
        return Order::salesStatuses();
    }

    private function resolveRange(string $range): array
    {
        $now = now();
        $key = in_array($range, ['7d', '30d', '90d', 'this_month'], true) ? $range : '30d';

        return match ($key) {
            '7d' => [
                'key' => '7d',
                'label_key' => 'last-7-days',
                'start' => $now->copy()->startOfDay()->subDays(6),
                'end' => $now->copy()->endOfDay(),
            ],
            '30d' => [
                'key' => '30d',
                'label_key' => 'last-30-days',
                'start' => $now->copy()->startOfDay()->subDays(29),
                'end' => $now->copy()->endOfDay(),
            ],
            '90d' => [
                'key' => '90d',
                'label_key' => 'last-90-days',
                'start' => $now->copy()->startOfDay()->subDays(89),
                'end' => $now->copy()->endOfDay(),
            ],
            'this_month' => [
                'key' => 'this_month',
                'label_key' => 'this-month',
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function buildDailySeries(Carbon $start, Carbon $end, $aggregates, int $decimals = 0): array
    {
        $labels = [];
        $series = [];

        foreach (CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()) as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->locale(app()->getLocale())->translatedFormat('d M');
            $series[] = round((float) ($aggregates[$key] ?? 0), $decimals);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function buildStatusDistribution($aggregates): array
    {
        $labels = [];
        $series = [];

        foreach (Order::statuses() as $status) {
            $labels[] = __('dashboard.order-status-' . str_replace('_', '-', $status));
            $series[] = (int) ($aggregates[$status] ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function buildPaymentMethodDistribution($aggregates): array
    {
        $labels = [];
        $series = [];

        foreach (Order::paymentMethods() as $method) {
            $labels[] = __('dashboard.payment-method-' . str_replace('_', '-', $method));
            $series[] = (int) ($aggregates[$method] ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }
}