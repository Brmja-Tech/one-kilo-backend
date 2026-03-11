<?php

namespace App\Services\Api\Commerce;

use App\Models\Order;
use App\Repositories\Api\Commerce\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    public function paginate(int $userId, array $filters): LengthAwarePaginator
    {
        return $this->orderRepository->paginateForUser($userId, $filters);
    }

    public function show(int $userId, string $reference): Order
    {
        return $this->orderRepository->findForUser($userId, $reference);
    }
}
