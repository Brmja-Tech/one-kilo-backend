<?php

namespace App\Livewire\Dashboard\Contacts;

use App\Models\Contact;
use Livewire\Component;
use Livewire\WithPagination;

class ContactsData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public int $perPage = 10;

    public ?Contact $selected = null;

    protected $listeners = [
        'deleteItem' => 'deleteItem',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function openShow(int $id): void
    {
        $contact = Contact::query()->find($id);

        if (! $contact) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->selected = $contact;
        $this->dispatch('openContactModal');
    }

    public function closeShow(): void
    {
        $this->selected = null;
        $this->dispatch('closeContactModal');
    }

    public function confirmDelete(int $id): void
    {
        $this->dispatch('contactDelete', id: $id);
    }

    public function deleteItem(int $id): void
    {
        $deleted = Contact::query()->whereKey($id)->delete();

        if (! $deleted) {
            $this->dispatch('notify', type: 'error', message: __('dashboard.no-data'));

            return;
        }

        $this->dispatch('notify', type: 'success', message: __('dashboard.item_deleted_successfully'));
        $this->dispatch('itemDeleted');

        if ($this->selected?->id === $id) {
            $this->closeShow();
        }
    }

    public function render()
    {
        $search = trim($this->search);

        $items = Contact::query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('subject', 'like', $term)
                        ->orWhere('message', 'like', $term);
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('dashboard.contacts.contacts-data', compact('items'));
    }
}
