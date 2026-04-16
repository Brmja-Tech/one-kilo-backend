<?php

namespace App\Livewire\Dashboard\Deliveries;

use App\Services\Dashboard\DeliveryService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Services\Dashboard\UserService;

class UserCreate extends Component
{
    use WithFileUploads;
    public $image, $full_name, $email, $phone, $password, $password_confirmation , $vehicle_type;
    protected $userService;
    protected $listeners = ['openUserCreateModal' => 'loadCountries'];


    public function boot(DeliveryService $userService)
    {
        $this->userService = $userService;
    }

    protected function rules()
    {
        return [
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif'],
            'full_name'      => ['required', 'max:150'],
            'email'          => ['required', 'email', 'max:200', Rule::unique('deliveries', 'email')],
            'phone'          => ['nullable', 'max:25'],
            'vehicle_type'          => ['nullable', 'max:25'],
            'password'       => ['required', 'min:6', 'confirmed'],
        ];
    }

    public function submit()
    {

        $data = $this->validate();

        $user = $this->userService->create($data);

        if (!$user) {
            $this->dispatch('somethingFailed');
            return;
        }

        $this->dispatch('userAddMs');
        $this->reset('image', 'full_name', 'email', 'password', 'phone', 'password_confirmation');
        $this->dispatch('createModalToggle');
        $this->dispatch('refreshData')->to(UserData::class);
    }

    public function render()
    {
        return view('dashboard.deliveries.user-create');
    }
}
