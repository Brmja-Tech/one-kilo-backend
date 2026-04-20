<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\DeliveryRepository;
use App\Repositories\Dashboard\UserRepository;
use App\Utils\ImageManger;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class DeliveryService
{
    use WithFileUploads;

    protected $userRepository, $imageManger;

    public function __construct(DeliveryRepository $userRepository, ImageManger $imageManger)
    {
        $this->userRepository = $userRepository;
        $this->imageManger = $imageManger;
    } // End construct method

    public function getUsers()
    {
        $users = $this->userRepository->getUsers();
        if (! $users) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $users;
    }  //End getUsers method

    public function getAllUsers($search)
    {
        $users = $this->userRepository->getAllUsers($search);
        if (! $users) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $users;
    }  //End getAllUsers method

    public function getUser($id)
    {
        $user = $this->userRepository->getUser($id);
        if (! $user) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $user;
    } //End getUser method

    public function getProfileData(int $id , $date): ?array
    {
        $user = $this->userRepository->getUserProfile($id);

        if (! $user) {
            return null;
        }

        return [
            'user' => $user,
            'orders' => $this->userRepository->getUserOrders($user->id,10,$date),
            'statistics' => $this->userRepository->statistics($user->id,10,$date),
        ];
    }

    public function create(array $data)
    {

        if (isset($data['image'])) {
            $data['image'] = $this->imageManger->uploadImage('/uploads/users/', $data['image']);
        } else {
            $data['image'] = 'uploads/images/image.png';
        }
        if (isset($data['national_id_image'])) {
            $data['national_id_image'] = $this->imageManger->uploadImage('/uploads/users/', $data['national_id_image']);
        } else {
            $data['national_id_image'] = 'uploads/images/national_id_image.png';
        }
        if (isset($data['license_image'])) {
            $data['license_image'] = $this->imageManger->uploadImage('/uploads/users/', $data['license_image']);
        } else {
            $data['license_image'] = 'uploads/images/image.png';
        }
        if (isset($data['vehicle_license_image'])) {
            $data['vehicle_license_image'] = $this->imageManger->uploadImage('/uploads/users/', $data['vehicle_license_image']);
        } else {
            $data['vehicle_license_image'] = 'uploads/images/image.png';
        }
        $data['email_verified_at'] = Carbon::now();
        $data['password'] = Hash::make($data['password']);

        return $this->userRepository->createUser($data);
    } //End create method

    public function destroy($id)
    {
        $user = self::getUser($id);
        $user = $this->userRepository->destroy($user);
        if (! $user) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $user;
    } // End destroy method

    public function changestatus($id)
    {
        $user = self::getUser($id);
        $user = $this->userRepository->changestatus($user);
        if (! $user) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $user;
    } // End changeStatus method


    public function changestatusapprove($id,$status)
    {
        $user = self::getUser($id);
        $user = $this->userRepository->changestatusapprove($user,$status);
        if (! $user) {
            flash()->error(__('validation.something-valid'));
            return redirect()->back();
        }
        return $user;
    } // End changeStatus method
}
