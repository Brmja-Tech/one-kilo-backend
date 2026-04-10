<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            @php
                $setting = \App\Models\Setting::first();
            @endphp
            <li class="nav-item me-auto"><a class="navbar-brand" href="{{ route('dashboard.home') }}"><span
                        class="brand-logo"><img src="{{ asset($setting->logo) }}"></span>
                    <h2 class="brand-text">{{ $setting->site_name }}</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pe-0" data-bs-toggle="collapse"><i
                        class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i
                        class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc"
                        data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="nav-item @yield('dashboard-active')"><a class="d-flex align-items-center"
                    href="{{ route('dashboard.home') }}"><i data-feather="home"></i><span
                        class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.home') }}</span></a>
            </li>
            <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Apps &amp; Pages</span><i
                    data-feather="more-horizontal"></i>
            </li>

            @can('roles')
                <li class="nav-item @yield('roles-open') @yield('createRole-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='align-justify'></i><span class="menu-title text-truncate"
                            data-i18n="Roles &amp; Permission">{{ __('dashboard.roles') }}</span></a>
                    <ul class="menu-content">
                        <li><a class="@yield('roles-active') d-flex align-items-center"
                                href="{{ route('dashboard.roles.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate" data-i18n="Roles">{{ __('dashboard.roles') }}</span></a>
                        </li>
                        <li><a class="@yield('createRole-active') d-flex align-items-center"
                                href="{{ route('dashboard.roles.create') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Permission">{{ __('dashboard.create-role') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('admins')
                <li class="nav-item @yield('admins-open') @yield('createAdmin-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='users'></i><span class="menu-title text-truncate">
                            {{ __('dashboard.admins') }}</span>
                        <span
                            class="badge badge-light-warning rounded-pill ms-auto me-1">{{ App\Models\Admin::count() }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('admins-active') d-flex align-items-center"
                                href="{{ route('dashboard.admins.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.admins') }}</span></a>
                        </li>
                        <li><a class="@yield('createAdmin-active') d-flex align-items-center"
                                href="{{ route('dashboard.admins.create') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Permission">{{ __('dashboard.create-admin') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('users')
                <li class="nav-item @yield('users-open') @yield('createUser-open')"><a class="d-flex align-items-center"
                        href="#"><i data-feather='users'></i><span class="menu-title text-truncate">
                            {{ __('dashboard.users') }}</span>
                        <span class="badge badge-light-warning rounded-pill ms-auto me-1"> {{ App\Models\User::count() }}
                        </span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('users-active') d-flex align-items-center"
                                href="{{ route('dashboard.users.index') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.users') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan


            @can('countries')
                <li class="nav-item @yield('countries-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.countries') }}"><i data-feather='flag'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.countries') }}</span></a>
                </li>
            @endcan

            @can('categories')
                <li class="nav-item @yield('categories-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.categories') }}"><i data-feather='grid'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.categories') }}</span></a>
                </li>
            @endcan

            @can('products')
                <li class="nav-item @yield('products-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.products') }}"><i data-feather='package'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.products') }}</span></a>
                </li>
            @endcan

            @can('variants')
                <li class="nav-item @yield('variants-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.variants') }}"><i data-feather='layers'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.variants') }}</span></a>
                </li>
            @endcan

            @can('orders')
                <li class="nav-item @yield('orders-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.orders') }}"><i data-feather='shopping-bag'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.orders') }}</span></a>
                </li>
            @endcan

            @can('contacts')
                <li class="nav-item @yield('contacts-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.contacts') }}"><i data-feather='message-square'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.contacts') }}</span></a>
                </li>
            @endcan

            @can('coupons')
                <li class="nav-item @yield('coupons-active')"><a class="d-flex align-items-center"
                        href="{{ route('dashboard.coupons') }}"><i data-feather='gift'></i><span
                            class="menu-title text-truncate" data-i18n="Email">{{ __('dashboard.coupons') }}</span></a>
                </li>
            @endcan



            @can('settings')
                <li class="nav-item @yield('settings-open')"><a class="d-flex align-items-center" href="#">
                        <i data-feather="settings"></i><span class="menu-title text-truncate"
                            data-i18n="Roles &amp; Permission">{{ __('dashboard.settings') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a class="@yield('settings-active') d-flex align-items-center"
                                href="{{ route('dashboard.settings') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.genral-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('banners-active') d-flex align-items-center"
                                href="{{ route('dashboard.banners') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.banners') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('about-active') d-flex align-items-center"
                                href="{{ route('dashboard.about.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.about-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('privacy-active') d-flex align-items-center"
                                href="{{ route('dashboard.privacy.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.privacy-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('terms-active') d-flex align-items-center"
                                href="{{ route('dashboard.terms.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.terms-setting') }}</span></a>
                        </li>
                    </ul>
                    <ul class="menu-content">
                        <li><a class="@yield('faqs-active') d-flex align-items-center"
                                href="{{ route('dashboard.faqs.setting') }}"><i data-feather="circle"></i><span
                                    class="menu-item text-truncate"
                                    data-i18n="Roles">{{ __('dashboard.faqs-settings') }}</span></a>
                        </li>
                    </ul>
                </li>
            @endcan

        </ul>
    </div>
</div>
