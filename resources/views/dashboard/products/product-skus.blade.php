<div>
    @if (!$product)
        <div class="alert alert-danger">{{ __('dashboard.no-data') }}</div>
    @elseif (!$product->has_variants)
        <div class="alert alert-warning">{{ __('dashboard.product-has-no-variants-note') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-2">
        <div class="fw-semibold">
            {{ __('dashboard.total') }}: {{ $skus->count() }}
        </div>

        <button type="button" class="btn btn-primary" wire:click="resetForm" data-bs-toggle="modal"
            data-bs-target="#createModal" @disabled(!$product || !$product->has_variants)>
            <i data-feather='plus'></i> {{ __('dashboard.add-sku') }}
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('dashboard.sku') }}</th>
                    <th>{{ __('dashboard.attributes') }}</th>
                    <th>{{ __('dashboard.price') }}</th>
                    <th>{{ __('dashboard.qty') }}</th>
                    <th>{{ __('dashboard.status') }}</th>
                    <th>{{ __('dashboard.sort-order') }}</th>
                    <th>{{ __('dashboard.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($skus as $index => $skuRow)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $skuRow->sku ?: '-' }}</td>
                        <td>{{ $skuRow->label() ?: '-' }}</td>
                        <td>{{ number_format((float) $skuRow->priceBeforeDiscount(), 2) }}</td>
                        <td>{{ (int) $skuRow->quantity }}</td>
                        <td>
                            <span class="badge {{ $skuRow->status ? 'bg-light-success' : 'bg-light-danger' }}">
                                {{ $skuRow->status ? __('dashboard.active') : __('dashboard.inactive') }}
                            </span>
                        </td>
                        <td>{{ (int) ($skuRow->sort_order ?? 0) }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-primary"
                                    title="{{ __('dashboard.update') }}" wire:click="editSku({{ $skuRow->id }})">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-danger"
                                    title="{{ __('dashboard.delete') }}"
                                    wire:click="confirmDeleteSku({{ $skuRow->id }})">
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

    <x-create-modal title="{{ __('dashboard.add-sku') }}">
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.sku') }}</label>
                <input type="text" wire:model="sku" placeholder="{{ __('dashboard.sku') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'sku'])
            </div>

            <div class="col-md-3">
                <label class="col-form-label">{{ __('dashboard.price') }}</label>
                <input type="number" step="0.01" min="0" wire:model="price"
                    placeholder="{{ __('dashboard.price') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'price'])
            </div>

            <div class="col-md-3">
                <label class="col-form-label">{{ __('dashboard.qty') }}</label>
                <input type="number" min="0" wire:model="quantity" placeholder="{{ __('dashboard.qty') }}"
                    class="form-control">
                @include('dashboard.includes.error', ['property' => 'quantity'])
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-6">
                <label class="col-form-label d-block">{{ __('dashboard.status') }}</label>
                <div class="d-flex align-items-center gap-2 pt-50">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" wire:model.live="status">
                    </div>
                    <span class="fw-semibold">{{ $status ? __('dashboard.active') : __('dashboard.inactive') }}</span>
                </div>
                @include('dashboard.includes.error', ['property' => 'status'])
            </div>

            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.sort-order') }}</label>
                <input type="number" min="0" wire:model="sort_order"
                    placeholder="{{ __('dashboard.sort-order') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'sort_order'])
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="col-form-label m-0">{{ __('dashboard.attributes') }}</label>
                    <button type="button" class="btn btn-sm btn-info" wire:click="addAttributeRow">
                        <i data-feather="plus"></i> {{ __('dashboard.add-attribute') }}
                    </button>
                </div>

                @foreach ($skuAttributes as $index => $attribute)
                    @php
                        $variantId = $attribute['variant_id'] ?? null;
                        $variant = $variants->firstWhere('id', (int) $variantId);
                    @endphp
                    <div class="row g-1 align-items-end mb-1" wire:key="attr-row-{{ $index }}">
                        <div class="col-md-5">
                            <label class="form-label">{{ __('dashboard.variant') }}</label>
                            <select class="form-select" wire:model.live="skuAttributes.{{ $index }}.variant_id">
                                <option value="">{{ __('dashboard.select-variant') }}</option>
                                @foreach ($variants as $variantOption)
                                    <option value="{{ $variantOption->id }}">{{ $variantOption->name }}</option>
                                @endforeach
                            </select>
                            @error("skuAttributes.{$index}.variant_id")
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.variant-item') }}</label>
                            <select class="form-select"
                                wire:model.live="skuAttributes.{{ $index }}.variant_item_id"
                                @disabled(!$variant)>
                                <option value="">{{ __('dashboard.select-variant-item') }}</option>
                                @if ($variant)
                                    @foreach ($variant->activeItems as $itemOption)
                                        <option value="{{ $itemOption->id }}">{{ $itemOption->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error("skuAttributes.{$index}.variant_item_id")
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-1 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="removeAttributeRow({{ $index }})" @disabled(count($skuAttributes) <= 1)>
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach

                @include('dashboard.includes.error', ['property' => 'skuAttributes'])
            </div>
        </div>
    </x-create-modal>

    <x-update-modal title="{{ __('dashboard.update') }}">
        <div class="row">
            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.sku') }}</label>
                <input type="text" wire:model="sku" placeholder="{{ __('dashboard.sku') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'sku'])
            </div>

            <div class="col-md-3">
                <label class="col-form-label">{{ __('dashboard.price') }}</label>
                <input type="number" step="0.01" min="0" wire:model="price"
                    placeholder="{{ __('dashboard.price') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'price'])
            </div>

            <div class="col-md-3">
                <label class="col-form-label">{{ __('dashboard.qty') }}</label>
                <input type="number" min="0" wire:model="quantity" placeholder="{{ __('dashboard.qty') }}"
                    class="form-control">
                @include('dashboard.includes.error', ['property' => 'quantity'])
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-6">
                <label class="col-form-label d-block">{{ __('dashboard.status') }}</label>
                <div class="d-flex align-items-center gap-2 pt-50">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" role="switch" wire:model.live="status">
                    </div>
                    <span class="fw-semibold">{{ $status ? __('dashboard.active') : __('dashboard.inactive') }}</span>
                </div>
                @include('dashboard.includes.error', ['property' => 'status'])
            </div>

            <div class="col-md-6">
                <label class="col-form-label">{{ __('dashboard.sort-order') }}</label>
                <input type="number" min="0" wire:model="sort_order"
                    placeholder="{{ __('dashboard.sort-order') }}" class="form-control">
                @include('dashboard.includes.error', ['property' => 'sort_order'])
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="col-form-label m-0">{{ __('dashboard.attributes') }}</label>
                    <button type="button" class="btn btn-sm btn-info" wire:click="addAttributeRow">
                        <i data-feather="plus"></i> {{ __('dashboard.add-attribute') }}
                    </button>
                </div>

                @foreach ($skuAttributes as $index => $attribute)
                    @php
                        $variantId = $attribute['variant_id'] ?? null;
                        $variant = $variants->firstWhere('id', (int) $variantId);
                    @endphp
                    <div class="row g-1 align-items-end mb-1" wire:key="attr-edit-row-{{ $index }}">
                        <div class="col-md-5">
                            <label class="form-label">{{ __('dashboard.variant') }}</label>
                            <select class="form-select"
                                wire:model.live="skuAttributes.{{ $index }}.variant_id">
                                <option value="">{{ __('dashboard.select-variant') }}</option>
                                @foreach ($variants as $variantOption)
                                    <option value="{{ $variantOption->id }}">{{ $variantOption->name }}</option>
                                @endforeach
                            </select>
                            @error("skuAttributes.{$index}.variant_id")
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{ __('dashboard.variant-item') }}</label>
                            <select class="form-select"
                                wire:model.live="skuAttributes.{{ $index }}.variant_item_id"
                                @disabled(!$variant)>
                                <option value="">{{ __('dashboard.select-variant-item') }}</option>
                                @if ($variant)
                                    @foreach ($variant->activeItems as $itemOption)
                                        <option value="{{ $itemOption->id }}">{{ $itemOption->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error("skuAttributes.{$index}.variant_item_id")
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-1 d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                wire:click="removeAttributeRow({{ $index }})" @disabled(count($skuAttributes) <= 1)>
                                <i data-feather="trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach

                @include('dashboard.includes.error', ['property' => 'skuAttributes'])
            </div>
        </div>
    </x-update-modal>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Livewire.on('skuDelete', function(data) {
                Swal.fire({
                    title: "{{ __('dashboard.are_you_sure') }}",
                    text: "{{ __('dashboard.confirm_delete_message') }}",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "{{ __('dashboard.yes_delete') }}",
                    cancelButtonText: "{{ __('dashboard.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('deleteSku', {
                            id: data.id
                        });
                    }
                });
            });
        });
    </script>
@endpush
