@extends('dashboard.master', ['title' => 'Profile'])
@section('profile-active', 'active')
@section('content')

    <!-- BEGIN: Content-->
    <div class="content-wrapper container-xxl p-0">

        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills mb-2">
                        <!-- account -->
                        <li class="nav-item">
                            <a class="nav-link @yield('profile-active')" href="{{route('dashboard.profile')}}">
                                <i class="font-medium-3 me-50 fa-solid fa-user"></i>
                                <span class="fw-bold">{{__('dashboard.account')}}</span>
                            </a>
                        </li>
                        <!-- security -->
                        <li class="nav-item">
                            <a class="nav-link @yield('security-active')" href="{{route('dashboard.security')}}">
                                <i class="font-medium-3 me-50 fa-solid fa-lock"></i>
                                <span class="fw-bold">{{__('dashboard.security')}}</span>
                            </a>
                        </li>
                    </ul>

                    <!-- profile -->
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">{{ __('dashboard.profile-details') }}</h4>
                        </div>
                        <div class="card-body py-2 my-25">
                            <!-- header section -->
                            <div class="d-flex">
                                <img src="{{asset(auth('admin')->user()->image ?? 'uploads/images/image.png')}}" id="account-upload-img"
                                    class="uploadedAvatar rounded me-50" alt="profile image" height="100"
                                    width="100" />
                            </div>
                            <!--/ header section -->

                            <!-- form -->
                            <hr>
                            <form action="{{route('dashboard.profile.update')}}" enctype="multipart/form-data" method="POST">
                                @csrf
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input type="file" id="singel-image" class="form-control" name="image"
                                            placeholder="{{ __('dashboard.image') }}">
                                            @include('dashboard.includes.error', ['property' => 'image'])
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="name" placeholder="{{ __('dashboard.name')}}"
                                            value="{{ auth('admin')->user()->name }}">
                                            @include('dashboard.includes.error', ['property' => 'name'])

                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="email" placeholder="{{ __('dashboard.email')}}"
                                            value="{{ auth('admin')->user()->email }}">
                                            @include('dashboard.includes.error', ['property' => 'email'])
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="facebook" placeholder="{{ __('dashboard.facebook-url')}}"
                                            value="{{ auth('admin')->user()->facebook }}">
                                            @include('dashboard.includes.error', ['property' => 'facebook'])
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="x_url" placeholder="{{ __('dashboard.x-url')}}"
                                            value="{{ auth('admin')->user()->x_url }}">
                                            @include('dashboard.includes.error', ['property' => 'x_url'])
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="linkedin" placeholder="{{ __('dashboard.linkedin')}}"
                                            value="{{ auth('admin')->user()->linkedin}}">
                                            @include('dashboard.includes.error', ['property' => 'linkedin'])
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="whatsapp" placeholder="{{ __('dashboard.whatsapp')}}"
                                            value="{{ auth('admin')->user()->whatsapp }}">
                                            @include('dashboard.includes.error', ['property' => 'whatsapp'])
                                    </div>
                                </div>
                                <div class=" mt-2 col-sm-9 offset-sm-3">
                                    <button type="submit"
                                        class="btn btn-primary waves-effect waves-float waves-light">{{ __('dashboard.submit') }}</button>
                                </div>
                            </form>
                            <!--/ form -->
                        </div>
                    </div>

                    <!-- deactivate account  -->
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Delete Account</h4>
                        </div>
                        <div class="card-body py-2 my-25">
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Are you sure you want to delete your account?</h4>
                                <div class="alert-body fw-normal">
                                    Once you delete your account, there is no going back. Please be certain.
                                </div>
                            </div>

                            <form id="formAccountDeactivation" class="validate-form" onsubmit="return false">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="accountActivation"
                                        id="accountActivation" data-msg="Please confirm you want to delete account" />
                                    <label class="form-check-label font-small-3" for="accountActivation">
                                        I confirm my account deactivation
                                    </label>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-danger deactivate-account mt-1">Deactivate
                                        Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--/ profile -->
                </div>
            </div>

        </div>
    </div>
    <!-- END: Content-->

@endsection
