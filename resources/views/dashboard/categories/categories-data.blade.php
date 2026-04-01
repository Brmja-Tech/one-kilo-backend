<div>
    <div class="row mb-1">
        <div class="col-md-4">
            <input type="text" class="form-control" wire:model.live="search"
                placeholder="{{ __('dashboard.search') }} ({{ __('dashboard.category') }} / {{ __('dashboard.slug') }})">
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="active">{{ __('dashboard.active') }}</option>
                <option value="inactive">{{ __('dashboard.inactive') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="structureFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="main">{{ __('dashboard.main-categories') }}</option>
                <option value="child">{{ __('dashboard.child-categories') }}</option>
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
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.category-image') }}</th>
                    <th>{{ __('dashboard.category') }}</th>
                    <th>{{ __('dashboard.slug') }}</th>
                    <th>{{ __('dashboard.parent-category') }}</th>
                    <th>{{ __('dashboard.color') }}</th>
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
                        <td>
                            @if ($item->image)
                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}"
                                    class="rounded border object-fit-cover" width="48" height="48">
                            @else
                                <span class="text-muted">{{ __('dashboard.no-image') }}</span>
                            @endif
                        </td>
                        <td>{{ $item->name }}</td>
                        <td><code>{{ $item->slug }}</code></td>
                        <td>{{ $item->parent?->name ?? __('dashboard.main-category') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-50">
                                <span class="rounded border d-inline-block"
                                    style="width: 18px; height: 18px; background-color: #{{ substr($item->color, 4) }};"></span>
                                <code>{{ $item->color }}</code>
                            </div>
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
                                    title="{{ __('dashboard.update-category') }}"
                                    wire:click="editCategory({{ $item->id }})">
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
                        <td colspan="10" class="text-center text-muted py-2">{{ __('dashboard.no-data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>
</div>
