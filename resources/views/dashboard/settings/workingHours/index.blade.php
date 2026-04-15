@extends('dashboard.master', ['title' => 'WorkingHours Setting'])
@section('workingHours-active', 'active')
@section('settings-open', 'open')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.workingHours-settings') }}</h4>
<!--                    <button type="button" class="btn btn-primary waves-effect" data-bs-toggle="modal"-->
<!--                        data-bs-target="#createModal">-->
<!--                        <i class="fa-solid fa-plus"></i> {{ __('dashboard.create-workingHours') }}-->
<!--                    </button>-->
                </div>

                <div class="card-body">
                    @livewire('dashboard.settings.workingHours.workingHours-data')
                </div>
                @livewire('dashboard.settings.workingHours.workingHours-update')
            </div>
        </div>
    </div>
@endsection
@push('js')
    {{-- Scripts from livewire success msg --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('workingHoursUpdateMS', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.update-successfully') }}',
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
            Livewire.on('workingHourAddMS', function() {
                Swal.fire({
                    position: 'top-start',
                    icon: 'success',
                    title: '{{ __('dashboard.add-successfully') }}',
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
            Livewire.on('StatusUpdateMS', function() {
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
            Livewire.on('workingHoursDelete', function(data) {
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
                Livewire.dispatch('refreshData');
                Swal.fire({
                    title: "{{ __('dashboard.success') }}",
                    text: "{{ __('dashboard.item_deleted_successfully') }}",
                    icon: "success",
                    timer: 1000
                });
            });
        });
    </script>
    {{-- End scripts from seewtalert delete livewire --}}
@endpush
