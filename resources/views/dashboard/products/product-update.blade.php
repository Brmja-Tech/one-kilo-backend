<x-update-modal title="{{ __('dashboard.update-product') }}">

    <div class="row">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.product-name-ar') }}</label>
            <input type="text" wire:model="name_ar" placeholder="{{ __('dashboard.product-name-ar') }}"
                class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_ar'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.product-name-en') }}</label>
            <input type="text" wire:model="name_en" placeholder="{{ __('dashboard.product-name-en') }}"
                class="form-control">
            @include('dashboard.includes.error', ['property' => 'name_en'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.category') }}</label>
            <select class="form-select" wire:model="category_id">
                <option value="">{{ __('dashboard.select-category') }}</option>
                @foreach ($categoryOptions as $option)
                    <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
            @include('dashboard.includes.error', ['property' => 'category_id'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.sku') }}</label>
            <input type="text" wire:model="sku" placeholder="{{ __('dashboard.sku') }}" class="form-control"
                @disabled($has_variants)>
            @include('dashboard.includes.error', ['property' => 'sku'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.short-description-ar') }}</label>
            <input type="text" wire:model="short_description_ar"
                placeholder="{{ __('dashboard.short-description-ar') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'short_description_ar'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.short-description-en') }}</label>
            <input type="text" wire:model="short_description_en"
                placeholder="{{ __('dashboard.short-description-en') }}" class="form-control">
            @include('dashboard.includes.error', ['property' => 'short_description_en'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.description-ar') }}</label>
            <textarea wire:model="description_ar" rows="4" class="form-control"
                placeholder="{{ __('dashboard.description-ar') }}"></textarea>
            @include('dashboard.includes.error', ['property' => 'description_ar'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.description-en') }}</label>
            <textarea wire:model="description_en" rows="4" class="form-control"
                placeholder="{{ __('dashboard.description-en') }}"></textarea>
            @include('dashboard.includes.error', ['property' => 'description_en'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.price') }}</label>
            <input type="number" step="0.01" min="0" wire:model="price" class="form-control"
                placeholder="{{ __('dashboard.price') }}" @disabled($has_variants)>
            @include('dashboard.includes.error', ['property' => 'price'])
        </div>

        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.stock') }}</label>
            <input type="number" min="0" wire:model="stock" class="form-control"
                placeholder="{{ __('dashboard.stock') }}" @disabled($has_variants)>
            @include('dashboard.includes.error', ['property' => 'stock'])
        </div>

        <div class="col-md-4">
            <label class="col-form-label d-block">{{ __('dashboard.featured') }}</label>
            <div class="d-flex align-items-center gap-2 pt-50">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model.live="is_featured">
                </div>
                <span class="fw-semibold">
                    {{ $is_featured ? __('dashboard.yes') : __('dashboard.no') }}
                </span>
            </div>
            @include('dashboard.includes.error', ['property' => 'is_featured'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.discount-type') }}</label>
            <select class="form-select" wire:model.live="discount_type">
                <option value="">{{ __('dashboard.select-discount-type') }}</option>
                <option value="amount">{{ __('dashboard.amount') }}</option>
                <option value="percentage">{{ __('dashboard.percentage') }}</option>
            </select>
            @include('dashboard.includes.error', ['property' => 'discount_type'])
        </div>

        <div class="col-md-4">
            <label class="col-form-label">{{ __('dashboard.discount-value') }}</label>
            <input type="number" step="0.01" min="0" wire:model="discount_value" class="form-control"
                placeholder="{{ __('dashboard.discount-value') }}" @disabled(blank($discount_type))>
            @include('dashboard.includes.error', ['property' => 'discount_value'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.discount-starts-at') }}</label>
            <input type="datetime-local" wire:model="discount_starts_at" class="form-control"
                @disabled(blank($discount_type))>
            @include('dashboard.includes.error', ['property' => 'discount_starts_at'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.discount-ends-at') }}</label>
            <input type="datetime-local" wire:model="discount_ends_at" class="form-control"
                @disabled(blank($discount_type))>
            @include('dashboard.includes.error', ['property' => 'discount_ends_at'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.main-image') }}</label>
            <input type="file" wire:model="image" class="form-control">
            @include('dashboard.includes.error', ['property' => 'image'])
        </div>

        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.add-gallery-images') }}</label>
            <input type="file" wire:model="gallery_images" class="form-control" multiple>
            @include('dashboard.includes.error', ['property' => 'gallery_images'])
            @include('dashboard.includes.error', ['property' => 'gallery_images.*'])
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-6">
            <label class="col-form-label">{{ __('dashboard.current-image') }}</label>
            <div>
                @if ($image)
                    <img src="{{ $image->temporaryUrl() }}" alt="{{ __('dashboard.main-image') }}"
                        class="rounded border object-fit-cover" width="130" height="130">
                @elseif ($currentImage)
                    <img src="{{ asset($currentImage) }}" alt="{{ __('dashboard.main-image') }}"
                        class="rounded border object-fit-cover" width="130" height="130">
                @else
                    <span class="text-muted">{{ __('dashboard.no-image') }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-12">
            <label class="col-form-label">{{ __('dashboard.current-gallery') }}</label>
            @if ($currentGallery !== [])
                <div class="row g-1">
                    @foreach ($currentGallery as $index => $galleryImage)
                        <div class="col-md-4" wire:key="existing-gallery-{{ $galleryImage['id'] }}">
                            <div class="border rounded p-1 h-100">
                                <img src="{{ asset($galleryImage['image']) }}" alt="{{ __('dashboard.gallery') }}"
                                    class="img-fluid rounded object-fit-cover mb-1"
                                    style="height: 140px; width: 100%;">

                                <label class="col-form-label">{{ __('dashboard.image-sort-order') }}</label>
                                <input type="number" min="0"
                                    wire:model="currentGallery.{{ $index }}.sort_order"
                                    class="form-control mb-1">
                                @include('dashboard.includes.error', [
                                    'property' => 'currentGallery.' . $index . '.sort_order',
                                ])

                                <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                    wire:click="removeExistingGalleryImage({{ $galleryImage['id'] }})">
                                    {{ __('dashboard.remove-image') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">{{ __('dashboard.no-images') }}</div>
            @endif
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-12">
            <label class="col-form-label">{{ __('dashboard.gallery') }}</label>
            @if ($gallery_images !== [])
                <div class="row g-1">
                    @foreach ($gallery_images as $index => $galleryImage)
                        <div class="col-md-3" wire:key="new-gallery-update-{{ $index }}">
                            <div class="border rounded p-1">
                                <img src="{{ $galleryImage->temporaryUrl() }}" alt="{{ __('dashboard.gallery') }}"
                                    class="img-fluid rounded object-fit-cover mb-1"
                                    style="height: 120px; width: 100%;">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100"
                                    wire:click="removeNewGalleryImage({{ $index }})">
                                    {{ __('dashboard.remove-image') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">{{ __('dashboard.no-images') }}</div>
            @endif
        </div>
    </div>

    <div class="row mt-1">
        <div class="col-md-4">
            <label class="col-form-label d-block">{{ __('dashboard.has-variants') }}</label>
            <div class="d-flex align-items-center gap-2 pt-50">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model.live="has_variants">
                </div>
                <span class="fw-semibold">
                    {{ $has_variants ? __('dashboard.yes') : __('dashboard.no') }}
                </span>
            </div>
            @include('dashboard.includes.error', ['property' => 'has_variants'])
        </div>
    </div>

    @if ($has_variants)
        <div class="row mt-2">
            <div class="col-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <span>{{ __('dashboard.variant-product-sku-note') }}</span>
                    @if ($productSlug)
                        <a href="{{ route('dashboard.products.skus', ['product' => $productSlug]) }}"
                            class="btn btn-sm btn-primary" target="_blank">
                            <i data-feather="layers"></i> {{ __('dashboard.manage-skus') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="row mt-2">
        <div class="col-md-6">
            <label class="col-form-label d-block">{{ __('dashboard.status') }}</label>
            <div class="d-flex align-items-center gap-2">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" wire:model.live="status">
                </div>
                <span class="fw-semibold">
                    {{ $status ? __('dashboard.active') : __('dashboard.inactive') }}
                </span>
            </div>
            @include('dashboard.includes.error', ['property' => 'status'])
        </div>
    </div>

</x-update-modal>
