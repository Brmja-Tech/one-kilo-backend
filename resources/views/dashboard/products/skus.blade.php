@extends('dashboard.master', ['title' => __('dashboard.product-skus')])
@section('products-active', 'active')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-1">
                    <div>
                        <h4 class="card-title mb-0">{{ __('dashboard.product-skus') }}</h4>
                        <small class="text-muted d-block">
                            {{ $product->name }} <code class="ms-50">{{ $product->slug }}</code>
                        </small>
                    </div>

                    <a href="{{ route('dashboard.products') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> {{ __('dashboard.back') }}
                    </a>
                </div>

                <div class="card-body">
                    @livewire('dashboard.products.product-skus', ['productId' => $product->id])
                </div>
            </div>
        </div>
    </div>
@endsection

