<?php

namespace App\Repositories\Dashboard;

use App\Models\Favorite;
use App\Models\Order;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

class UserRepository
{
    public function getUsers()
    {
        return User::get();
    } // End getUsers method

    public function getAllUsers($search)
    {
        return User::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        })
            ->latest()
            ->paginate(10);
    } // End getAllUsers method

    public function getUser($id)
    {
        $user = User::find($id);
        if (! $user) {
            return false;
        }
        return $user;
    } // End getUser method

    public function getUserProfile(int $id): ?User
    {
        return User::query()
            ->with([
                'country:id,name',
                'governorate:id,name',
                'region:id,governorate_id,name',
                'wallet:id,user_id,balance,status,created_at',
            ])
            ->withCount([
                'orders',
                'favorites',
                'walletTransactions',
            ])
            ->find($id);
    }

    public function getUserWalletTransactions(int $userId, int $limit = 10): Collection
    {
        return WalletTransaction::query()
            ->where('user_id', $userId)
            ->with('order:id,order_number')
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function getUserOrders(int $userId, int $limit = 10): Collection
    {
        return Order::query()
            ->where('user_id', $userId)
            ->withCount('items')
            ->withSum('items as items_quantity_sum', 'quantity')
            ->latest('placed_at')
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

    public function getUserFavorites(int $userId, int $limit = 12): Collection
    {
        return Favorite::query()
            ->where('user_id', $userId)
            ->with([
                'product:id,name,slug,image,price,discount_type,discount_value,discount_starts_at,discount_ends_at,sku,stock,status',
            ])
            ->latest('id')
            ->limit($limit)
            ->get();
    }

    public function createUser(array $data)
    {
        return User::create($data);
    }

    public function updateUser($request, $country) {} // End updateUser method

    public function destroy($user)
    {
        if ($user->image && $user->image !== 'uploads/images/image.png') {
            @unlink(public_path($user->image));
        }
        return $user->delete();
    } // End destroy method

    public function changestatus($user)
    {
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();
        return $user;
    } // End changestatus method
}
