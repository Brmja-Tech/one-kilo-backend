@extends('dashboard.master', [
    'title' => __('dashboard.variants'),
])

@section('variants-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.variants') }}</h4>

                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-variant') }}
                    </button>

                    @livewire('dashboard.variants.variant-create')
                </div>

                <div class="card-body">
                    @livewire('dashboard.variants.variants-data')
                    @livewire('dashboard.variants.variant-update')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('variantDelete', function(data) {
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
                    text: "{{ __('dashboard.deleted-successfully') }}",
                    icon: "success",
                    timer: 2000
                });
            });
        });
    </script>
@endpush
