<div>
    <div class="d-flex flex-wrap gap-1 align-items-center justify-content-between mb-1">
        <div class="d-flex gap-1 align-items-center" style="min-width: 260px;">
            <input type="text" class="form-control" placeholder="{{ __('dashboard.search') }}"
                wire:model.live="search">
        </div>
        <div class="d-flex gap-1 align-items-center">
            <select class="form-select" wire:model.live="perPage" style="width: 120px;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.name') }}</th>
                    <th>{{ __('dashboard.email') }}</th>
                    <th>{{ __('dashboard.phone') }}</th>
                    <th>{{ __('dashboard.contact-subject') }}</th>
                    <th>{{ __('dashboard.date') }}</th>
                    <th class="text-end">{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone }}</td>
                        <td class="text-truncate" style="max-width: 280px;">{{ $item->subject }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                wire:click="openShow({{ $item->id }})">
                                <i class="fa-regular fa-eye"></i> {{ __('dashboard.view') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="confirmDelete({{ $item->id }})">
                                <i class="fa-regular fa-trash-can"></i> {{ __('dashboard.delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">
                            {{ __('dashboard.no-data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>

    {{-- Show Modal --}}
    <div class="modal fade" id="contactShowModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('dashboard.contact-details') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="closeShow"></button>
                </div>
                <div class="modal-body">
                    @if ($selected)
                        <div class="row">
                            <div class="col-md-6 mb-1">
                                <div class="fw-bold">{{ __('dashboard.name') }}</div>
                                <div>{{ $selected->name }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="fw-bold">{{ __('dashboard.email') }}</div>
                                <div>{{ $selected->email }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="fw-bold">{{ __('dashboard.phone') }}</div>
                                <div>{{ $selected->phone }}</div>
                            </div>
                            <div class="col-md-6 mb-1">
                                <div class="fw-bold">{{ __('dashboard.date') }}</div>
                                <div>{{ $selected->created_at?->format('Y-m-d H:i') }}</div>
                            </div>

                            <div class="col-12 mb-1">
                                <div class="fw-bold">{{ __('dashboard.contact-subject') }}</div>
                                <div>{{ $selected->subject }}</div>
                            </div>
                            <div class="col-12">
                                <div class="fw-bold">{{ __('dashboard.contact-message') }}</div>
                                <div class="border rounded p-1" style="white-space: pre-wrap;">{{ $selected->message }}</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        wire:click="closeShow">{{ __('dashboard.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Livewire.on('contactDelete', function(data) {
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

                Livewire.on('openContactModal', () => {
                    const el = document.getElementById('contactShowModal');
                    if (!el) return;
                    const modal = bootstrap.Modal.getOrCreateInstance(el);
                    modal.show();
                });

                Livewire.on('closeContactModal', () => {
                    const el = document.getElementById('contactShowModal');
                    if (!el) return;
                    const modal = bootstrap.Modal.getInstance(el);
                    modal && modal.hide();
                });
            });
        </script>
    @endpush
</div>
