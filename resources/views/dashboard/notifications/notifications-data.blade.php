<div class="table-responsive">
    <div class="card-header">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.title') }}</th>
                <th>{{ __('dashboard.message') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->message }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a class="btn btn-danger waves-effect waves-float waves-light" href="#"
                                    data-id="{{ $item->id }}"
                                    wire:click.prevent="$dispatch('notificationDelete', {id: {{ $item->id }}})"
                                    title="{{ __('dashboard.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4">
                        <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class=" mt-2">
        {{ $data->links() }}
    </div>
</div>
