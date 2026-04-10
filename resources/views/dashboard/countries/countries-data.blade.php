<div class="table-responsive">
    <div class="card-header">
        <input type="text" wire:model.live.debounce.300ms="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search') }}">
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.governorates') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td>{{ ($data->firstItem() ?? 0) + $loop->index }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-secondary">
                                {{ $item->governorates_count }}
                            </span>

                            <a class="btn btn-sm btn-info waves-effect waves-float waves-light"
                                href="{{ route('dashboard.countries.governorates', $item) }}"
                                title="{{ __('dashboard.manage-governorates') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                                {{ __('dashboard.manage') }}
                            </a>
                        </div>
                    </td>

                    <td>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" {{ $item->status ? 'checked' : '' }}
                                wire:click="updateStatus({{ $item->id }}, {{ $item->status ? 0 : 1 }})">
                        </div>
                    </td>

                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-sm btn-primary waves-effect waves-float waves-light" href="#"
                                title="{{ __('dashboard.update') }}"
                                wire:click.prevent="editCountry({{ $item->id }})">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>

                            <a class="btn btn-sm btn-danger ms-2 waves-effect waves-float waves-light" href="#"
                                wire:click.prevent="confirmDelete({{ $item->id }})"
                                title="{{ __('dashboard.delete') }}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-2">
        {{ $data->links() }}
    </div>
</div>
