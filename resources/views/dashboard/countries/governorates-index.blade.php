@extends('dashboard.master', ['title' => __('dashboard.governorates')])
@section('countries-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <div>
                        <h4 class="card-title mb-0">{{ __('dashboard.governorates') }}</h4>
                        <small class="text-muted d-block">{{ $country->name }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <a href="{{ route('dashboard.countries') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left"></i> {{ __('dashboard.back') }}
                        </a>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-governorate') }}
                        </button>
                    </div>

                    @livewire('dashboard.governorates.governorate-create', ['countryId' => $country->id])
                </div>

                <div class="card-body">
                    @livewire('dashboard.governorates.governorates-data', ['countryId' => $country->id])
                    @livewire('dashboard.governorates.governorate-update', ['countryId' => $country->id])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('governorateDelete', function(data) {
                Swal.fire({
                    title: "{{ __('dashboard.are_you_sure') }}",
                    text: "{{ __('dashboard.confirm_delete_message') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('dashboard.yes_delete') }}",
                    cancelButtonText: "{{ __('dashboard.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteItem', {
                            id: data.id
                        });
                    }
                });
            });

            window.addEventListener('itemDeleted', function() {
                Swal.fire({
                    title: "{{ __('dashboard.success') }}",
                    text: "{{ __('dashboard.item_deleted_successfully') }}",
                    icon: "success",
                    timer: 2000
                });
            });
        });
    </script>
@endpush

