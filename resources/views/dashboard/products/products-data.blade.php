<div>
    <div class="row mb-1 g-1">
        <div class="col-md-3">
            <input type="text" class="form-control" wire:model.live="search"
                placeholder="{{ __('dashboard.search') }} ({{ __('dashboard.product') }} / {{ __('dashboard.sku') }} / {{ __('dashboard.slug') }})">
        </div>

        <div class="col-md-2">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="active">{{ __('dashboard.active') }}</option>
                <option value="inactive">{{ __('dashboard.inactive') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" wire:model.live="categoryFilter">
                <option value="all">{{ __('dashboard.all') }} {{ __('dashboard.categories') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category['id'] }}">{{ $category['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-select" wire:model.live="featuredFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="featured">{{ __('dashboard.featured-products') }}</option>
                <option value="regular">{{ __('dashboard.regular-products') }}</option>
            </select>
        </div>

        <div class="col-md-1">
            <select class="form-select" wire:model.live="stockFilter">
                <option value="all">{{ __('dashboard.all') }}</option>
                <option value="in_stock">{{ __('dashboard.in-stock') }}</option>
                <option value="out_of_stock">{{ __('dashboard.out-of-stock') }}</option>
            </select>
        </div>

        <div class="col-md-1">
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
                    <th>{{ __('dashboard.image') }}</th>
                    <th>{{ __('dashboard.product') }}</th>
                    <th>{{ __('dashboard.sku') }}</th>
                    <th>{{ __('dashboard.category') }}</th>
                    <th>{{ __('dashboard.price') }}</th>
                    <th>{{ __('dashboard.stock') }}</th>
                    <th>{{ __('dashboard.featured') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
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
                                    class="rounded border object-fit-cover" width="52" height="52">
                                @if ($item->images_count > 0)
                                    <small class="d-block text-muted mt-50">
                                        {{ __('dashboard.product-images') }}: {{ $item->images_count }}
                                    </small>
                                @endif
                            @else
                                <span class="text-muted">{{ __('dashboard.no-image') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $item->getTranslation('name', 'en') }}</div>
                            <small class="d-block text-muted">{{ $item->getTranslation('name', 'ar') }}</small>
                            <small class="d-block"><code>{{ $item->slug }}</code></small>
                        </td>
                        <td>{{ $item->sku ?: '-' }}</td>
                        <td>
                            <div>{{ $item->category?->name ?? '-' }}</div>
                            @if ($item->category && ! $item->category->status)
                                <small class="text-warning">{{ __('dashboard.inactive') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ number_format((float) $item->priceAfterDiscount(), 2) }}</div>
                            @if ($item->hasActiveDiscount())
                                <small class="d-block text-muted text-decoration-line-through">
                                    {{ number_format((float) $item->priceBeforeDiscount(), 2) }}
                                </small>
                                <small class="badge bg-light-warning">
                                    @if ($item->discount_type === 'percentage')
                                        {{ rtrim(rtrim(number_format((float) $item->discount_value, 2), '0'), '.') }}%
                                    @else
                                        -{{ number_format((float) $item->discount_value, 2) }}
                                    @endif
                                </small>
                            @else
                                <small class="text-muted">{{ __('dashboard.no-discount') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $item->stock > 0 ? 'bg-light-success' : 'bg-light-danger' }}">
                                {{ $item->stock > 0 ? __('dashboard.in-stock') : __('dashboard.out-of-stock') }}
                            </span>
                            <small class="d-block text-muted">{{ __('dashboard.qty') }}: {{ $item->stock }}</small>
                        </td>
                        <td style="min-width: 150px">
                            <div class="form-check form-switch form-check-warning">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    id="toggle_featured_{{ $item->id }}" @checked($item->is_featured)
                                    wire:click="updateFeatured({{ $item->id }}, {{ $item->is_featured ? 0 : 1 }})">
                                <label class="form-check-label" for="toggle_featured_{{ $item->id }}">
                                    {{ $item->is_featured ? __('dashboard.featured') : __('dashboard.no') }}
                                </label>
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
                        <td>{{ $item->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary"
                                    title="{{ __('dashboard.update-product') }}"
                                    wire:click="editProduct({{ $item->id }})">
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
                        <td colspan="11" class="text-center text-muted py-2">{{ __('dashboard.no-data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-1">
        {{ $items->links() }}
    </div>
</div>
