@extends('dashboard.master', ['title' => 'Roles'])
@section('roles-active', 'active')
@section('roles-open', 'open')
@section('content')
    <div class="row" id="basic-table">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('dashboard.all_roles') }}</h4>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('dashboard.role') }}</th>
                                <th>{{ __('dashboard.premession') }}</th>
                                <th>{{ __('dashboard.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($roles->count() > 0)
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $role->role }}</td>
                                        <td>
                                            @if (config('app.locale') == 'ar')
                                                @foreach (json_decode($role->permession) as $perm)
                                                    @foreach (Config::get('permessions_ar') as $key => $value)
                                                    <span class="badge rounded-pill bg-primary">{{ $key == $perm ? $value : '' }}</span>
                                                    @endforeach
                                                @endforeach
                                            @else
                                                @foreach (json_decode($role->permession) as $perm)
                                                    @foreach (Config::get('permessions_en') as $key => $value)
                                                    <span class="badge rounded-pill bg-primary">{{ $key == $perm ? $value : '' }}</span>
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <div class="demo-inline-spacing">
                                                <a href="{{ route('dashboard.roles.edit', $role->id) }}"
                                                    title="{{ __('dashboard.update') }}"
                                                    class="btn btn-info waves-effect waves-float waves-light">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="javascript:void(0);" title="{{ __('dashboard.delete') }}"
                                                    class="btn btn-danger waves-effect waves-float waves-light"
                                                    id="confirm-delete-text" data-form-id="delete-form-{{ $role->id }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $role->id }}"
                                                    action="{{ route('dashboard.roles.destroy', $role->id) }}"
                                                    method="post" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                @endforeach
                                </tr>
                            @else
                            <td colspan="4"><div class="text-danger text-center">{{ __('dashboard.no-data') }}</div></td>
                            @endif
                        </tbody>
                    </table>
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
