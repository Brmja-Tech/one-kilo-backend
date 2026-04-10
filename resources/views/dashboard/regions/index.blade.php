@extends('dashboard.master', ['title' => __('dashboard.regions')])
@section('countries-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <div>
                        <h4 class="card-title mb-0">{{ __('dashboard.regions') }}</h4>
                        <small class="text-muted d-block">
                            {{ $governorate->name }}
                            @if ($governorate->country)
                                <span class="ms-25">— {{ $governorate->country->name }}</span>
                            @endif
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <a href="{{ route('dashboard.countries.governorates', $governorate->country_id) }}"
                            class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left"></i> {{ __('dashboard.back') }}
                        </a>

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-region') }}
                        </button>
                    </div>

                    @livewire('dashboard.regions.region-create', ['governorateId' => $governorate->id])
                </div>

                <div class="card-body">
                    @livewire('dashboard.regions.regions-data', ['governorateId' => $governorate->id])
                    @livewire('dashboard.regions.region-update', ['governorateId' => $governorate->id])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('regionDelete', function(data) {
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

