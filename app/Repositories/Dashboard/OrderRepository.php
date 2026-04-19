<?php

namespace App\Repositories\Dashboard;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderStatusLog;

class OrderRepository
{
    public function loadDetails(Order $order): Order
    {
        return $order->load([
            'user:id,name,email,phone',
            'address:id,country_id,governorate_id,region_id,label,contact_name,phone,city,area,street,building_number,floor,apartment_number,landmark,status',
            'address.country:id,name',
            'address.governorate:id,country_id,name',
            'address.region:id,governorate_id,name,shipping_price',
            'coupon:id,code',
            'walletTransaction:id,wallet_id,user_id,order_id,type,transaction_type,amount,balance_before,balance_after,reference,notes,status,created_at',
            'items:id,order_id,product_id,product_name,product_image,unit_price,quantity,line_total',
            'items.product:id,sku,slug',
            'statusLogs.changedByAdmin:id,name',
        ]);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update([
            'status' => $status,
        ]);

        return $order->fresh();
    }

    public function createStatusLog(Order $order, array $data): OrderStatusLog
    {
        return $order->statusLogs()->create($data);
    }


    public function availableDeliveries()
    {
        // Available (approved + not busy)
        $availableDeliveries = Delivery::where('status', 'approved')
            ->whereDoesntHave('orders', function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            })
            ->get();

        return $availableDeliveries;
    }


    public function busyDeliveries()
    {
        // Busy Deliveries
        $busyDeliveries = Delivery::where('status', 'approved')
            ->whereHas('orders', function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            })
            ->with(['orders' => function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            }])
            ->get();

        return $busyDeliveries;
    }


    public function assign($request ,$orderId){

        $request->validate([
            'delivery_id' => 'required|exists:deliveries,id',
        ]);

        $order = Order::findOrFail($orderId);

        $delivery = Delivery::where('id', $request->delivery_id)
            ->where('status', 'approved')
            ->firstOrFail();

        $order->update([
            'delivery_id' => $delivery->id,
        ]);
    }
}
