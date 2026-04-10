@extends('dashboard.master', ['title' => 'Admins'])
@section('admins-active', 'active')
@section('admins-open', 'open')
@section('content')
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.all_admins') }}</h4>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.name') }}</th>
                                <th>{{ __('dashboard.email') }}</th>
                                <th>{{ __('dashboard.status') }}</th>
                                <th>{{ __('dashboard.role') }}</th>
                                <th>{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($admins->count() > 0)
                                @foreach ($admins as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            @if ($item->status == 1)
                                                <button
                                                    class="change-status-btn btn btn-sm btn-success waves-effect waves-float waves-light"
                                                    data-url="{{ route('dashboard.admin.changeStatus', $item->id) }}">
                                                    {{ __('dashboard.active') }}
                                                </button>
                                            @else
                                                <button
                                                    class="change-status-btn btn btn-sm btn-warning waves-effect waves-float waves-light"
                                                    data-url="{{ route('dashboard.admin.changeStatus', $item->id) }}">
                                                    {{ __('dashboard.inactive') }}
                                                </button>
                                            @endif
                                        </td>
                                        <td>{{ $item->role->role }}</td>
                                        <td>
                                            <div class="demo-inline-spacing">
                                                <a href="{{ route('dashboard.admins.edit', $item->id) }}"
                                                    title="{{ __('dashboard.update') }}"
                                                    class="btn btn-info waves-effect waves-float waves-light">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0);" title="{{ __('dashboard.delete') }}"
                                                    class="btn btn-danger waves-effect waves-float waves-light"
                                                    id="confirm-delete-text" data-form-id="delete-form-{{ $item->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('dashboard.admins.destroy', $item->id) }}"
                                                    method="post" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                @endforeach
                                </tr>
                            @else
                                <td colspan="6">
                                    <div class="text-danger text-center">{{ __('dashboard.no-data') }}</div>
                                </td>
                            @endif
                        </tbody>
                    </table>
                    {{ $admins->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
