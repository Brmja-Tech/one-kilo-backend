<div>
    <div class="row mb-1 g-1">
        <div class="col-md-4">
            <input type="text" class="form-control" wire:model.live="search"
                placeholder="{{ __('dashboard.search') }} ({{ __('dashboard.name') }} / {{ __('dashboard.key') }})">
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="active">{{ __('dashboard.active') }}</option>
                <option value="inactive">{{ __('dashboard.inactive') }}</option>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.name') }}</th>
                    <th>{{ __('dashboard.key') }}</th>
                    <th>{{ __('dashboard.items-count') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                    <th>{{ __('dashboard.sort-order') }}</th>
                    <th>{{ __('dashboard.created-at') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td>{{ $items->firstItem() + $index }}</td>
                        <td class="fw-semibold">{{ $item->name }}</td>
                        <td><code>{{ $item->key }}</code></td>
                        <td>
                            <span class="badge bg-light-primary">{{ (int) $item->items_count }}</span>
                        </td>
                        <td style="min-width: 140px">
                            <div class="form-check form-switch form-check-success">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="toggle_status_{{ $item->id }}" @checked($item->status)
                                    wire:click="updateStatus({{ $item->id }}, {{ $item->status ? 0 : 1 }})">
                                <label class="form-check-label" for="toggle_status_{{ $item->id }}">
                                    {{ $item->status ? __('dashboard.active') : __('dashboard.inactive') }}
                                </label>
                            </div>
                        </td>
                        <td>{{ $item->sort_order ?? '-' }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary"
                                    title="{{ __('dashboard.update') }}"
                                    wire:click="editVariant({{ $item->id }})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-danger"
                                    title="{{ __('dashboard.delete') }}"
                                    wire:click="confirmDelete({{ $item->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-2">{{ __('dashboard.no-data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>
</div>
