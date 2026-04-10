@extends('dashboard.master', ['title' => 'Users'])
@section('users-active', 'active')
@section('users-open', 'open')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.users') }}</h4>
                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-user') }}
                    </button>
                    @livewire('dashboard.users.user-create')
                </div>
                <div class="card-body">
                    @livewire('dashboard.users.user-data')
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{-- Scripts from livewire success msg --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('somethingFailed', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'error',
                    title: '{{ __('validation.something-valid') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('userAddMs', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.user-add-successfully') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('userStatusUpdate', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.status-change') }}',
                    showConfirmButton: false,
                    timer: 1500,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            });
        });
    </script>
    {{-- End scripts from livewire success msg --}}
    {{-- Scripts from seewtalert delete livewire --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('userDelete', function(data) {
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
    {{-- End scripts from seewtalert delete livewire --}}
@endpush
