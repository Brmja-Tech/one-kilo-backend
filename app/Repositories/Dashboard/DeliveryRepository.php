<?php

namespace App\Repositories\Dashboard;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeliveryRepository
{
    public function getUsers()
    {
        return Delivery::get();
    } // End getUsers method

    public function getAllUsers($search)
    {
        return Delivery::when($search, function ($query) use ($search) {
            $query->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->latest()
            ->paginate(10);
    } // End getAllUsers method

    public function getUser($id)
    {
        $user = Delivery::find($id);
        if (! $user) {
            return false;
        }
        return $user;
    } // End getUser method

    public function getUserProfile(int $id)
    {
       return Delivery::query()
            ->withCount([
                'orders as delivered_orders_count' => function ($q) {
                    $q->where('status', 'delivered');
                },
                'orders as confirmed_orders_count' => function ($q) {
                    $q->whereIn('status', ['confirmed','out_for_delivery','preparing','picked_up']);
                },
            ])
            ->find($id);
    }



    public function getUserOrders(int $userId, int $limit = 10,$date = null): Collection
    {
        $all_orders = Order::query()
            ->where('delivery_id', $userId)
            ->withCount('items')
            ->withSum('items as items_quantity_sum', 'quantity')
            ->latest('placed_at')
            ->latest('id')
            ->limit($limit);

        if($date){
            $all_orders  = $all_orders->whereDate('created_at', $date);
        }
        $all_orders =   $all_orders->get([
                'id',
                'delivery_id',
                'order_number',
                'status',
                'payment_method',
                'payment_status',
                'total',
                'placed_at',
                'created_at',
            ]);
        return $all_orders;
    }

    public function statistics(int $userId, int $limit = 10,$date = null)
    {
        $statistics = DB::table('orders')
            ->selectRaw("
            COUNT(CASE WHEN status = 'delivered' THEN 1 END) AS delivered_orders_count,

            SUM(CASE WHEN status = 'delivered' THEN delivery_fee ELSE 0 END) AS total_delivery_fee_delivered,

            COUNT(CASE WHEN payment_method = 'cash_on_delivery' THEN 1 END) AS cash_orders_count,
            SUM(CASE WHEN payment_method = 'cash_on_delivery' THEN total ELSE 0 END) AS cash_total_amount,
            SUM(CASE WHEN payment_method = 'cash_on_delivery' THEN delivery_fee ELSE 0 END) AS cash_delivery_fee,

            COUNT(CASE WHEN payment_method = 'card' THEN 1 END) AS card_orders_count,
            SUM(CASE WHEN payment_method = 'card' THEN total ELSE 0 END) AS card_total_amount,
            SUM(CASE WHEN payment_method = 'card' THEN delivery_fee ELSE 0 END) AS card_delivery_fee,
             COUNT(*) AS delivered_orders_count,
            COALESCE(SUM(total), 0) AS total_orders_amount,
            COALESCE(SUM(delivery_fee), 0) AS total_delivery_fee
        ")
            ->where('delivery_id', $userId);
        if($date){
            $statistics  = $statistics->whereDate('created_at', $date);
        }

       return $statistics->first();
    }

    public function createUser(array $data)
    {
        return Delivery::create($data);
    }

    public function updateUser($request, $country) {} // End updateUser method

    public function destroy($user)
    {
        if ($user->image && $user->image !== 'uploads/images/image.png') {
            @unlink(public_path($user->image));
        }
        if ($user->national_id_image && $user->national_id_image !== 'uploads/images/image.png') {
            @unlink(public_path($user->national_id_image));
        }
        if ($user->license_image && $user->license_image !== 'uploads/images/image.png') {
            @unlink(public_path($user->license_image));
        }
        if ($user->vehicle_license_image && $user->vehicle_license_image !== 'uploads/images/image.png') {
            @unlink(public_path($user->vehicle_license_image));
        }
        return $user->delete();
    } // End destroy method

    public function changestatus($user)
    {
        $user->login_status = $user->login_status == 1 ? 0 : 1;
        $user->save();
        return $user;
    } // End changestatus method


    public function changestatusapprove($user,$status)
    {
        $user->status = $status;
        $user->save();
        return $user;
    } // End changestatus method
}
