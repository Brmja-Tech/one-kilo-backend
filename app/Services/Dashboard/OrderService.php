<?php

namespace App\Services\Dashboard;

use App\Models\Admin;
use App\Models\Order;
use App\Repositories\Dashboard\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    public function getOrderDetails(Order $order): Order
    {
        return $this->orderRepository->loadDetails($order);
    }

    public function updateStatus(Order $order, string $newStatus, ?Admin $admin = null): Order
    {
        $oldStatus = $order->status;

        if (! $order->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => __('dashboard.invalid-order-status-transition'),
            ]);
        }

        return DB::transaction(function () use ($order, $oldStatus, $newStatus, $admin) {
            $updatedOrder = $this->orderRepository->updateStatus($order, $newStatus);

            $this->orderRepository->createStatusLog($updatedOrder, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by_admin_id' => $admin?->id,
                'title' => __('dashboard.order-status-updated'),
                'description' => __('dashboard.order-status-changed-description', [
                    'from' => $this->statusLabel($oldStatus),
                    'to' => $this->statusLabel($newStatus),
                ]),
                'meta' => [
                    'source' => 'dashboard',
                ],
            ]);

            return $this->orderRepository->loadDetails($updatedOrder);
        });
    }

    private function statusLabel(string $status): string
    {
        return __('dashboard.order-status-' . str_replace('_', '-', $status));
    }


    public function getDeliveries($orderId){

        $order = Order::findOrFail($orderId);

        $availableDeliveries = $this->orderRepository->availableDeliveries();

        $busyDeliveries = $this->orderRepository->busyDeliveries();


        $data = array(
            "order" => $order,
            "availableDeliveries" => $availableDeliveries,
            "busyDeliveries" => $busyDeliveries
        );

        return $data;

    }

    public function assign($request,$orderId){

        return $this->orderRepository->assign($request,$orderId);
    }
}
