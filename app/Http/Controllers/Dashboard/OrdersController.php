<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrdersController extends Controller
{
    public function index()
    {
        return view('dashboard.orders.index');
    }

    public function show(Order $order)
    {
        $order->load([
            'user:id,name,email,phone',
            'address:id,country_id,governorate_id,label,contact_name,phone,city,area,street,building_number,floor,apartment_number,landmark,status',
            'address.country:id,name',
            'address.governorate:id,country_id,name,shipping_price',
            'coupon:id,code',
            'walletTransaction:id,wallet_id,user_id,order_id,type,transaction_type,amount,balance_before,balance_after,reference,notes,status,created_at',
            'items:id,order_id,product_id,product_name,product_image,unit_price,quantity,line_total',
            'items.product:id,sku,slug',
        ]);

        return view('dashboard.orders.show', [
            'order' => $order,
            'address' => $this->resolveAddress($order),
            'couponSnapshot' => (array) data_get($order->meta, 'coupon_snapshot', []),
        ]);
    }

    private function resolveAddress(Order $order): array
    {
        $snapshot = $order->addressSnapshot();

        if ($snapshot !== []) {
            return [
                'id' => $snapshot['id'] ?? $order->address_id,
                'label' => $snapshot['label'] ?? null,
                'contact_name' => $snapshot['contact_name'] ?? null,
                'phone' => $snapshot['phone'] ?? null,
                'country_name' => $snapshot['country_name'] ?? null,
                'governorate_name' => $snapshot['governorate_name'] ?? null,
                'city' => $snapshot['city'] ?? null,
                'area' => $snapshot['area'] ?? null,
                'street' => $snapshot['street'] ?? null,
                'building_number' => $snapshot['building_number'] ?? null,
                'floor' => $snapshot['floor'] ?? null,
                'apartment_number' => $snapshot['apartment_number'] ?? null,
                'landmark' => $snapshot['landmark'] ?? null,
                'full_address' => $snapshot['full_address'] ?? null,
            ];
        }

        if (! $order->address) {
            return [];
        }

        return [
            'id' => $order->address->id,
            'label' => $order->address->label,
            'contact_name' => $order->address->contact_name,
            'phone' => $order->address->phone,
            'country_name' => $order->address->country?->name,
            'governorate_name' => $order->address->governorate?->name,
            'city' => $order->address->city,
            'area' => $order->address->area,
            'street' => $order->address->street,
            'building_number' => $order->address->building_number,
            'floor' => $order->address->floor,
            'apartment_number' => $order->address->apartment_number,
            'landmark' => $order->address->landmark,
            'full_address' => $order->address->fullAddress(),
        ];
    }
}
