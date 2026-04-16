<?php

namespace App\Livewire\Dashboard\Deliveries;

use App\Services\Dashboard\DeliveryService;
use App\Services\Dashboard\UserService;
use Livewire\Component;
use Livewire\WithPagination;

class UserData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];
    protected $userService;
    public $search;

    public function boot(DeliveryService $userService)
    {
        $this->userService = $userService;
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function updateStatus($itemId)
    {
        $this->userService->changestatus($itemId);
        $this->dispatch('userStatusUpdate');
    }


    public function updateStatusApprove($itemId,$status)
    {
        $this->userService->changestatusapprove($itemId,$status);
        $this->dispatch('userStatusUpdate');
    }


    public function deleteItem($id)
    {
        $this->userService->destroy($id);
        $this->dispatch('itemDeleted');
        $this->dispatch('refreshData');
    }

    public function render()
    {
        $data = $this->userService->getAllUsers($this->search);
        return view('dashboard.deliveries.user-data', compact('data'));
    }
}
