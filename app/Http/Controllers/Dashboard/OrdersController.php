<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Delivery;
use App\Models\Order;
use App\Services\Dashboard\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index()
    {
        return view('dashboard.orders.index');
    }

    public function show(Order $order)
    {
        $data = $this->buildOrderViewData($order);

        return view('dashboard.orders.show', [
            ...$data,
            'allowedNextStatuses' => $data['order']->allowedNextStatuses(),
            'canChangeStatus' => (bool) auth('admin')->user()?->hasAccess('orders_change_status'),
        ]);
    }

    public function print(Order $order)
    {
        return view('dashboard.orders.print', [
            ...$this->buildOrderViewData($order),
            'printedAt' => now(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->orderService->updateStatus(
            $order,
            (string) $request->string('status'),
            $request->user('admin')
        );

        flash()->success(__('dashboard.status-updated-successfully'));

        return back();
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
                'region_name' => $snapshot['region_name'] ?? null,
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
            'region_name' => $order->address->region?->name,
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

    private function buildOrderViewData(Order $order): array
    {
        $order = $this->orderService->getOrderDetails($order);

        return [
            'order' => $order,
            'address' => $this->resolveAddress($order),
            'couponSnapshot' => (array) data_get($order->meta, 'coupon_snapshot', []),
        ];
    }


    public function assignDelivery($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Available (approved + not busy)
        $availableDeliveries = Delivery::where('status', 'approved')
            ->whereDoesntHave('orders', function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            })
            ->get();



        // Busy Deliveries
        $busyDeliveries = Delivery::where('status', 'approved')
            ->whereHas('orders', function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            })
            ->with(['orders' => function ($q) {
                $q->whereNotIn('status', ['picked_up', 'delivered']);
            }])
            ->get();


       return view('dashboard.orders.assign-delivery', compact(
          'order',
           'availableDeliveries',
           'busyDeliveries'
       ));
    }

    public function assign(Request $request, $orderId)
    {
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

        return redirect()
            ->route('dashboard.orders.show', $order->id)
            ->with('success', 'Delivery assigned successfully.');
    }
}
