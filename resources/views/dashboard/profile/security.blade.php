@extends('dashboard.master', ['title' => 'Profile Security'])
@section('security-active', 'active')
@section('content')

    <!-- BEGIN: Content-->
    <div class="content-wrapper container-xxl p-0">

        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-pills mb-2">
                        <!-- account -->
                        <li class="nav-item">
                            <a class="nav-link @yield('profile-active')" href="{{ route('dashboard.profile') }}">
                                <i class="font-medium-3 me-50 fa-solid fa-user"></i>
                                <span class="fw-bold">{{ __('dashboard.account') }}</span>
                            </a>
                        </li>
                        <!-- security -->
                        <li class="nav-item">
                            <a class="nav-link @yield('security-active')" href="{{ route('dashboard.security') }}">
                                <i class="font-medium-3 me-50 fa-solid fa-lock"></i>
                                <span class="fw-bold">{{ __('dashboard.security') }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">{{__('dashboard.change-password')}}</h4>
                        </div>
                        <div class="card-body pt-1">
                            <!-- Form -->
                            <form action="{{ route('dashboard.profile.update.password') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <!-- Current Password -->
                                    <div class="col-12 col-sm-6 mb-1">
                                        <label class="form-label">{{ __('dashboard.current-password') }}</label>
                                        <div class="input-group form-password-toggle input-group-merge">
                                            <input type="password" class="form-control"
                                                name="current_password" placeholder="{{ __('dashboard.current-password') }}"
                                                required />
                                            <div class="input-group-text cursor-pointer">
                                                <i class="fa-solid fa-eye"></i>
                                            </div>
                                            @include('dashboard.includes.error' , ['property' => 'current_password'])
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- New Password -->
                                    <div class="col-12 col-sm-6 mb-1">
                                        <label class="form-label"
                                            for="account-new-password">{{ __('dashboard.new-password') }}</label>
                                        <div class="input-group form-password-toggle input-group-merge">
                                            <input type="password" id="account-new-password" name="new_password"
                                                class="form-control" placeholder="{{ __('dashboard.new-password') }}"
                                                required />
                                            <div class="input-group-text cursor-pointer">
                                                <i class="fa-solid fa-eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Confirm New Password -->
                                    <div class="col-12 col-sm-6 mb-1">
                                        <label class="form-label"
                                            for="account-retype-new-password">{{ __('dashboard.confirm-new-password') }}</label>
                                        <div class="input-group form-password-toggle input-group-merge">
                                            <input type="password" class="form-control" id="account-retype-new-password"
                                                name="new_password_confirmation"
                                                placeholder="{{ __('dashboard.confirm-new-password') }}" required />
                                            <div class="input-group-text cursor-pointer">
                                                <i class="fa-solid fa-eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Password Requirements -->
                                    <div class="col-12">
                                        <p class="fw-bolder">{{__('dashboard.password-requirements')}}</p>
                                        <ul class="ps-1 ms-25">
                                            <li class="mb-50">{{__('dashboard.minimum-8-characters')}}</li>
                                            <li class="mb-50">{{__('dashboard.at-least-one-lowercase-character')}}</li>
                                            <li class="mb-50">{{__('dashboard.at-least-one-uppercase-character')}}</li>
                                            <li>{{__('dashboard.at-least-one-number-symbol-or-whitespace-character')}}</li>
                                        </ul>
                                    </div>
                                    <!-- Buttons -->
                                    <div class="col-12">
                                        <button type="submit"
                                            class="btn btn-primary me-1 mt-1">{{ __('dashboard.submit') }}</button>
                                        <button type="reset"
                                            class="btn btn-outline-secondary mt-1">{{ __('dashboard.cancel') }}</button>
                                    </div>
                                </div>
                            </form>
                            <!--/ Form -->
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- END: Content-->

@endsection
