<?php

namespace App\Livewire\Dashboard\Notifications;

use App\Models\AdminNotification;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function deleteItem($id)
    {
        $item = AdminNotification::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
        if (!$item) return;
    }


    public function render()
    {
        $data = AdminNotification::where(function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('message', 'like', '%' . $this->search . '%');
        })
            ->latest()
            ->paginate(10);
        return view('dashboard.notifications.notifications-data', compact('data'));
    }
}
