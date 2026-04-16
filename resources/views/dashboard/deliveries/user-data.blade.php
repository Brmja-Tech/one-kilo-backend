<div class="table-responsive" wire:ignore.self>
    <div class="card-header ">
        <input type="text" wire:model.live="search" class="form-control w-25"
            placeholder="{{ __('dashboard.search-here') }}">
    </div>
    <table class="table">
        <thead>
            <tr>
                {{-- <th>#</th> --}}
                <th>{{ __('dashboard.image') }}</th>
                <th>{{ __('dashboard.name') }}</th>
                <th>{{ __('dashboard.email') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.status') }}</th>
                <th>{{ __('dashboard.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @if ($data->count() > 0)
                @foreach ($data as $item)
                    <tr>
                        {{-- <td>{{ $loop->iteration }}</td> --}}
                        <td>
                            <img src="{{ asset($item->image) }}" alt="image" width="80">
                        </td>
                        <td>{{ $item->full_name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox"
                                    {{ $item->login_status == 1 ? 'checked' : '' }}
                                    wire:click="updateStatus({{ $item->id }})">
                            </div>
                        </td>

                        <td>

                                <select class="form-control"
                                        wire:change="updateStatusApprove({{ $item->id }}, $event.target.value)">

                                    <option value="pending"  {{ $item->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $item->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $item->status == 'rejected' ? 'selected' : '' }}>Rejected</option>

                                </select>

                        </td>

                        <td>
                            <div class="d-flex align-items-center">
                                <a class="btn btn-info waves-effect waves-float waves-light"
                                    title="{{ __('dashboard.show') }}"
                                    href="{{ route('dashboard.deliveries.profile', ['id' => $item->id]) }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>

                                <a class="btn btn-danger waves-effect waves-float waves-light" href="#"
                                    data-id="{{ $item->id }}"
                                    wire:click.prevent="$dispatch('userDelete', {id: {{ $item->id }}})"
                                    title="{{ __('dashboard.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <td colspan="6">
                    <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                </td>
            @endif
        </tbody>
    </table>
    <div class=" mt-2">
        {{ $data->links() }}
    </div>
</div>
