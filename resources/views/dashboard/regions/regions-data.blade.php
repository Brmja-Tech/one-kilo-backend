<div>
    <div class="row mb-1 g-1">
        <div class="col-md-6">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="{{ __('dashboard.search') }} ({{ __('dashboard.region') }})">
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="active">{{ __('dashboard.active') }}</option>
                <option value="inactive">{{ __('dashboard.inactive') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="perPage">
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
                    <th>{{ __('dashboard.shipping-price') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ number_format((float) $item->shipping_price, 2) }}</td>
                        <td style="min-width: 160px;">
                            <div class="form-check form-switch form-check-success">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="toggle_status_{{ $item->id }}" @checked($item->status)
                                    wire:click="updateStatus({{ $item->id }}, {{ $item->status ? 0 : 1 }})">
                                <label class="form-check-label" for="toggle_status_{{ $item->id }}">
                                    {{ $item->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                </label>
                            </div>
                        </td>
                        <td style="min-width: 180px;">
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary"
                                    title="{{ __('dashboard.update-region') }}" wire:click="editRegion({{ $item->id }})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="{{ __('dashboard.delete') }}"
                                    wire:click="confirmDelete({{ $item->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-2">{{ __('dashboard.no-data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>
</div>

