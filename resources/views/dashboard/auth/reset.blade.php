@extends('dashboard.auth.partials.auth', ['title' => 'Login'])

@section('content')

    <form class="auth-login-form mt-2" action="{{ route('dashboard.password.reset') }}" method="POST">
        @csrf
        <div class="mb-1">
            <input hidden type="text" class="form-control" value="{{$email}}" name="email"/>
        </div>
        <div class="mb-1">
            <div class="d-flex justify-content-between">
                <label class="form-label">{{ __('auth.password') }}</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge" name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                <span class="input-group-text cursor-pointer"><i class="fa-solid fa-eye"></i></span>
            </div>
        </div>
        @error('password')
            <div class="text-danger">{{ $message }}</div>
        @enderror
        <div class="mb-1">
            <div class="d-flex justify-content-between">
                <label class="form-label">{{ __('auth.password-confirmation') }}</label>
            </div>
            <div class="input-group input-group-merge form-password-toggle">
                <input type="password" class="form-control form-control-merge" name="password_confirmation"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                <span class="input-group-text cursor-pointer"><i class="fa-solid fa-eye"></i></span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">{{ __('auth.send') }}</button>
    </form>
@endsection
