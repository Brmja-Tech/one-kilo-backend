@extends('dashboard.master', ['title' => __('dashboard.contacts')])
@section('contacts-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.contacts') }}</h4>
                </div>
                <div class="card-body">
                    @livewire('dashboard.contacts.contacts-data')
                </div>
            </div>
        </div>
    </div>
@endsection
