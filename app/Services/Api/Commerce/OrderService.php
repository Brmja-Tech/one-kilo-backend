<?php

namespace App\Services\Api\Commerce;

use App\Models\Order;
use App\Repositories\Api\Commerce\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepository , protected FirebaseService $firebaseService)
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

    public function showForDelivery(int $userId, string $reference): Order
    {
        return $this->orderRepository->findForDelivery($userId, $reference);
    }

    public function currentOrders(int $userId, array $filters): LengthAwarePaginator
    {
        return $this->orderRepository->paginateCurrentForDelivery($userId, $filters);
    }

    public function pastOrders(int $userId, array $filters): LengthAwarePaginator
    {
        return $this->orderRepository->paginatePastForDelivery($userId, $filters);
    }

    public function updateStatus(string $orderId,$request)
    {
        return $this->orderRepository->updateStatusForDelivery($orderId,$request);

    }

    public function getNotifications(){
        return $this->orderRepository->getNotifications();
    }
}
