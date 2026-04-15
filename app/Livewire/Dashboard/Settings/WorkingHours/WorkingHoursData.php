<?php

namespace App\Livewire\Dashboard\Settings\WorkingHours;

use App\Models\WorkingHour;
use Livewire\Component;
use Livewire\WithPagination;

class WorkingHoursData extends Component
{
    use WithPagination;
    protected $listeners = ['refreshData' => '$refresh', 'deleteItem'];
    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updateStatus($itemId, $newStatus)
    {
        $item = WorkingHour::find($itemId);
        if (!$item) return;
        $item->status = $newStatus;
        $item->save();
        $this->dispatch('StatusUpdateMS');
    }

    public function deleteItem($id)
    {
        $item = WorkingHour::find($id);
        if ($item) {
            $item->delete();
            $this->dispatch('itemDeleted');
        }
        if (!$item) return;
    }


    public function render()
    {
        $data = WorkingHour::where('day_name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);
        return view('dashboard.settings.workingHours.workingHours-data', compact('data'));
    }
}
