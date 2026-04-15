<div class="table-responsive">
    <div class="card-header">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('dashboard.day_name') }}</th>
                <th>{{ __('dashboard.open_time') }}</th>
                <th>{{ __('dashboard.close_time') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->day_name }}</td>
                        <td>{{ $item->open_time }}</td>
                        <td>{{ $item->close_time }}</td>
                        <td>{{ $item->status }}</td>


                        <td>
                            <div class="d-flex align-items-center">
                                <a class="btn btn-primary waves-effect waves-float waves-light"
                                    title="{{ __('dashboard.update') }}" href="#"
                                    wire:click.prevent="$dispatch('workingHoursUpdate', {id: {{ $item->id }}})">
                                    <i class="fa-regular fa-pen-to-square"></i>
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
