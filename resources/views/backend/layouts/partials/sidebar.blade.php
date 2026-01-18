@php
    $userGuard = Auth::guard('admin')->user();
@endphp
<div class="main-nav">
    <!-- Sidebar Logo -->
    <div class="logo-box">
        {{--        <a href="{{ route('admin.home') }}" class="logo-dark">--}}
        {{--            <img src="{{ asset('frontend-office/assets/images/logo.png') }}" class="logo-sm" alt="logo sm">--}}
        {{--            <img src="{{ asset('frontend-office/assets/images/logo.png') }}" class="logo-lg" alt="logo dark">--}}
        {{--        </a>--}}

        <a href="{{ route('admin.home') }}" class="logo-light">
            <img style="max-width: 170px;" src="{{ asset('images/'.\App\Models\Setting::get('logo')) }}" class="logo-sm"
                 alt="logo sm">
            {{ \App\Models\Setting::get('company_name') }}
            {{--            <img style="max-width: 200px;" src="{{ asset('frontend-office/assets/images/logo.png') }}" class="logo-lg" alt="logo light">--}}
        </a>
    </div>

    <!-- Menu Toggle Button (sm-hover) -->
    <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
        <iconify-icon icon="solar:hamburger-menu-broken" class="button-sm-hover-icon"></iconify-icon>
    </button>

    <div class="scrollbar" data-simplebar>

        <ul class="navbar-nav" id="navbar-nav">

            <li class="menu-title">Menu</li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.home') }}">
                                   <span class="nav-icon">
                                        <iconify-icon icon="solar:home-2-broken"></iconify-icon>
                                   </span>
                    <span class="nav-text"> Dashboard </span>
                </a>
            </li>
            <li class="nav-item {{ Route::is('admin.settings.edit',auth()->id()) ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.settings.edit',auth()->id()) }}">
                                   <span class="nav-icon">
                <i class="fas fa-cog"></i> </span>
                    <span class="nav-text"> Setting </span>
                </a>
            </li>
            <li class="nav-item {{ Route::is('admin.change') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.change') }}">
                                   <span class="nav-icon">
                <i class="fas fa-key"></i> </span>
                    <span class="nav-text"> Update Password </span>
                </a>
            </li>

            <li class="menu-title">Components</li>
            {{--   Branch's   --}}
            @if ( $userGuard->can('branches.index') || $userGuard->can('branches.create') || $userGuard->can('branches.edit') || $userGuard->can('branches.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.branches.create') || Route::is('admin.branches.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarBranch" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarBranch">
                                <span class="nav-icon">
                                    <i class="fas fa-code-branch"></i>
                                </span>
                        <span class="nav-text"> Branch's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.branches.create') || Route::is('admin.branches.index') ? 'active' : 'collapse' }}"
                        id="sidebarBranch">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.branches.create') }}">{{ __('language.create') }}</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.branches.index') }}">{{ __('language.list') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Role's   --}}
            @if ( $userGuard->can('roles.index') || $userGuard->can('roles.create') || $userGuard->can('roles.edit') || $userGuard->can('roles.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.roles.create') || Route::is('admin.roles.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarRole" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarRole">
                                <span class="nav-icon">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                        <span class="nav-text"> Role's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.roles.create') || Route::is('admin.roles.index') ? 'active' : 'collapse' }}"
                        id="sidebarRole">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.roles.create') }}">{{ __('language.create') }}</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.roles.index') }}">{{ __('language.list') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif


            {{--   Admin's   --}}
            @if ( $userGuard->can('admins.index') || $userGuard->can('admins.create') || $userGuard->can('admins.edit') || $userGuard->can('admins.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarAdmin" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarAdmin">
                                <span class="nav-icon">
                                    <i class="fas fa-users-cog"></i>
                                </span>
                        <span class="nav-text"> Admin's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.admins.create') || Route::is('admin.admins.index') ? 'active' : 'collapse' }}"
                        id="sidebarAdmin">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.admins.create') }}">{{ __('language.create') }}</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.admins.index') }}">{{ __('language.list') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif




            {{--   User's   --}}
            @if ( $userGuard->can('admins.index') || $userGuard->can('admins.create') || $userGuard->can('admins.edit') || $userGuard->can('admins.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.users.create') || Route::is('admin.users.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarUser" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarUser">
                                <span class="nav-icon">
                                    <i class="fas fa-user-injured"></i>
                                </span>
                        <span class="nav-text"> Patients's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.users.create') || Route::is('admin.users.index') ? 'active' : 'collapse' }}"
                        id="sidebarUser">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.users.create') }}">{{ __('language.create') }}</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.users.index') }}">{{ __('language.list') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif
            {{--   Admit's   --}}
            @if ($userGuard->can('admits.index') || $userGuard->can('admits.create') || $userGuard->can('admits.edit') || $userGuard->can('admits.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.admits.create') || Route::is('admin.admits.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarAdmit" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarAdmit">
                        <span class="nav-icon">
                            <i class="fas fa-procedures"></i>
                        </span>
                        <span class="nav-text"> {{ __('Admit') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.admits.create') || Route::is('admin.admits.index') ? 'active' : 'collapse' }}"
                        id="sidebarAdmit">
                        <ul class="nav sub-navbar-nav">
                            {{-- @if ($userGuard->can('admits.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="{{ route('admin.admits.create') }}">
                                        {{ __('language.create') }}
                                    </a>
                                </li>
                            @endif --}}
                            @if ($userGuard->can('admits.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="{{ route('admin.admits.index') }}">
                                        {{ __('language.list') }}
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Category's   --}}
            @if ( $userGuard->can('categories.index') || $userGuard->can('categories.create') || $userGuard->can('categories.edit') || $userGuard->can('categories.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.categories.create') || Route::is('admin.categories.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarCategory" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarCategory">
                                <span class="nav-icon">
                                    <i class="fas fa-layer-group"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.category') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.categories.create') || Route::is('admin.categories.index') ? 'active' : 'collapse' }}"
                        id="sidebarCategory">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('categories.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.categories.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('categories.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.categories.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Product's (Test)   --}}
            @if ( $userGuard->can('products.index') || $userGuard->can('products.create') || $userGuard->can('products.edit') || $userGuard->can('products.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.products.create') || Route::is('admin.products.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarProduct" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarProduct">
                                <span class="nav-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.test') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.products.create') || Route::is('admin.products.index') ? 'active' : 'collapse' }}"
                        id="sidebarProduct">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('products.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.products.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('products.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.products.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Reefer's (Refer)   --}}
            @if ( $userGuard->can('reefers.index') || $userGuard->can('reefers.create') || $userGuard->can('reefers.edit') || $userGuard->can('reefers.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.reefers.create') || Route::is('admin.reefers.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarReefer" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarReefer">
                                <span class="nav-icon">
                                    <i class="fas fa-user-tie"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.dr_refer') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.reefers.create') || Route::is('admin.reefers.index') ? 'active' : 'collapse' }}"
                        id="sidebarReefer">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('reefers.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reefers.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('reefers.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reefers.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('reefers.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reefers.custom-sms') }}">Sms alert</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Invoice's   --}}
            @if ( $userGuard->can('invoices.index') || $userGuard->can('invoices.create') || $userGuard->can('invoices.edit') || $userGuard->can('invoices.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.invoices.create') || Route::is('admin.invoices.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarInvoice" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarInvoice">
                                <span class="nav-icon">
                                    <i class="fas fa-user-md"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.invoice') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.invoices.create') || Route::is('admin.invoices.index') ? 'active' : 'collapse' }}"
                        id="sidebarInvoice">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('invoices.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.invoices.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('invoices.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.invoices.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   ServiceCategory's   --}}
            @if ( $userGuard->can('service_categories.index') || $userGuard->can('service_categories.create') || $userGuard->can('service_categories.edit') || $userGuard->can('service_categories.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.service_categories.create') || Route::is('admin.service_categories.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarServiceCategory" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarServiceCategory">
                        <span class="nav-icon">
                                <i class="fas fa-briefcase-medical"></i>
                        </span>
                        <span class="nav-text">Service {{ __('Category') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.service_categories.create') || Route::is('admin.service_categories.index') ? 'active' : 'collapse' }}"
                        id="sidebarServiceCategory">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('service_categories.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.service_categories.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('service_categories.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.service_categories.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   Service's   --}}
            @if ( $userGuard->can('services.index') || $userGuard->can('services.create') || $userGuard->can('services.edit') || $userGuard->can('services.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.services.create') || Route::is('admin.services.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarService" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarService">
                        <span class="nav-icon">
                                <i class="fas fa-briefcase-medical"></i>
                        </span>
                        <span class="nav-text"> Service's</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.services.create') || Route::is('admin.services.index') ? 'active' : 'collapse' }}"
                        id="sidebarService">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('services.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.services.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('services.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.services.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Recept's   --}}
            @if ($userGuard->can('recepts.index') || $userGuard->can('recepts.create') || $userGuard->can('recepts.edit') || $userGuard->can('recepts.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.recepts.create') || Route::is('admin.recepts.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarRecept" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarRecept">
                        <span class="nav-icon">
                            <i class="fas fa-receipt"></i>
                        </span>
                        <span class="nav-text">{{ __('Recept') }}</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.recepts.create') || Route::is('admin.recepts.index') ? 'active' : 'collapse' }}"
                        id="sidebarRecept">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('recepts.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.recepts.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('recepts.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.recepts.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   ReceptList's   --}}
            {{-- @if ($userGuard->can('receptlists.index') || $userGuard->can('receptlists.create') || $userGuard->can('receptlists.edit') || $userGuard->can('receptlists.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.receptlists.create') || Route::is('admin.receptlists.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarReceptList" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarReceptList">
                        <span class="nav-icon">
                            <i class="fas fa-list-alt"></i>
                        </span>
                        <span class="nav-text">{{ __('Recept List') }}</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.receptlists.create') || Route::is('admin.receptlists.index') ? 'active' : 'collapse' }}"
                        id="sidebarReceptList">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('receptlists.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.receptlists.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('receptlists.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.receptlists.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif --}}

            {{--   Pharmacy Category's   --}}
            @if ( $userGuard->can('pharmacy_categories.index') || $userGuard->can('pharmacy_categories.create') || $userGuard->can('pharmacy_categories.edit') || $userGuard->can('pharmacy_categories.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_categories.create') || Route::is('admin.pharmacy_categories.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyCategory" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyCategory">
                                   <span class="nav-icon">
                                        <i class="fas fa-pills"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Categories </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_categories.create') || Route::is('admin.pharmacy_categories.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyCategory">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_categories.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_categories.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_categories.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_categories.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Brand's   --}}
            @if ( $userGuard->can('pharmacy_brands.index') || $userGuard->can('pharmacy_brands.create') || $userGuard->can('pharmacy_brands.edit') || $userGuard->can('pharmacy_brands.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_brands.create') || Route::is('admin.pharmacy_brands.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyBrand" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyBrand">
                                   <span class="nav-icon">
                                        <i class="fas fa-capsules"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Brands </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_brands.create') || Route::is('admin.pharmacy_brands.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyBrand">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_brands.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_brands.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_brands.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_brands.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Type's   --}}
            @if ( $userGuard->can('pharmacy_types.index') || $userGuard->can('pharmacy_types.create') || $userGuard->can('pharmacy_types.edit') || $userGuard->can('pharmacy_types.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_types.create') || Route::is('admin.pharmacy_types.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyType" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyType">
                                   <span class="nav-icon">
                                        <i class="fas fa-syringe"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Types </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_types.create') || Route::is('admin.pharmacy_types.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyType">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_types.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_types.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_types.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_types.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Unit's   --}}
            @if ( $userGuard->can('pharmacy_units.index') || $userGuard->can('pharmacy_units.create') || $userGuard->can('pharmacy_units.edit') || $userGuard->can('pharmacy_units.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_units.create') || Route::is('admin.pharmacy_units.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyUnit" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyUnit">
                                   <span class="nav-icon">
                                        <i class="fas fa-ruler-horizontal"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Units </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_units.create') || Route::is('admin.pharmacy_units.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyUnit">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_units.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_units.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_units.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_units.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Product's   --}}
            @if ( $userGuard->can('pharmacy_products.index') || $userGuard->can('pharmacy_products.create') || $userGuard->can('pharmacy_products.edit') || $userGuard->can('pharmacy_products.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_products.create') || Route::is('admin.pharmacy_products.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyProduct" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyProduct">
                                   <span class="nav-icon">
                                        <i class="fas fa-prescription-bottle"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Products </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_products.create') || Route::is('admin.pharmacy_products.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyProduct">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_products.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_products.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_products.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_products.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Sale's   --}}
            @if ( $userGuard->can('pharmacy_sales.index') || $userGuard->can('pharmacy_sales.create') || $userGuard->can('pharmacy_sales.edit') || $userGuard->can('pharmacy_sales.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_sales.create') || Route::is('admin.pharmacy_sales.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacySale" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacySale">
                                   <span class="nav-icon">
                                        <i class="fas fa-cash-register"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Sales </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_sales.create') || Route::is('admin.pharmacy_sales.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacySale">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_sales.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_sales.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_sales.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_sales.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Pharmacy Purchase's   --}}
            @if ( $userGuard->can('pharmacy_purchases.index') || $userGuard->can('pharmacy_purchases.create') || $userGuard->can('pharmacy_purchases.edit') || $userGuard->can('pharmacy_purchases.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.pharmacy_purchases.create') || Route::is('admin.pharmacy_purchases.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPharmacyPurchase" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPharmacyPurchase">
                                   <span class="nav-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                   </span>
                        <span class="nav-text"> Pharmacy Purchases </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.pharmacy_purchases.create') || Route::is('admin.pharmacy_purchases.index') ? 'active' : 'collapse' }}"
                        id="sidebarPharmacyPurchase">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('pharmacy_purchases.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_purchases.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('pharmacy_purchases.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.pharmacy_purchases.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Employee's   --}}
            @if ( $userGuard->can('employees.index') || $userGuard->can('employees.create') || $userGuard->can('employees.edit') || $userGuard->can('employees.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.employees.create') || Route::is('admin.employees.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarEmployee" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarEmployee">
                                   <span class="nav-icon">
                                        <i class="fas fa-user-secret"></i>
                                   </span>
                        <span class="nav-text"> {{ __('language.employee') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.employees.create') || Route::is('admin.employees.index') ? 'active' : 'collapse' }}"
                        id="sidebarEmployee">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('employees.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.employees.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('employees.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.employees.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
            {{--   Earn's   --}}
            @if ( $userGuard->can('earns.index') || $userGuard->can('earns.create') || $userGuard->can('earns.edit') || $userGuard->can('earns.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.earns.create') || Route::is('admin.earns.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarEarn" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarEarn">
                                <span class="nav-icon">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                        <span class="nav-text">Earn</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.earns.create') || Route::is('admin.earns.index') ? 'active' : 'collapse' }}"
                        id="sidebarEarn">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('earns.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.earns.create') }}">Transaction</a>
                                </li>
                            @endif
                            @if ($userGuard->can('earns.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.earns.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   CostCategory's   --}}
            @if ( $userGuard->can('cost_categories.index') || $userGuard->can('cost_categories.create') || $userGuard->can('cost_categories.edit') || $userGuard->can('cost_categories.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.cost_categories.create') || Route::is('admin.cost_categories.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarCostCategory" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarCostCategory">
                                <span class="nav-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.cost') }} {{ __('language.category') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.cost_categories.create') || Route::is('admin.cost_categories.index') ? 'active' : 'collapse' }}"
                        id="sidebarCostCategory">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('cost_categories.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.cost_categories.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('cost_categories.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.cost_categories.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   Cost's   --}}
            @if ( $userGuard->can('costs.index') || $userGuard->can('costs.create') || $userGuard->can('costs.edit') || $userGuard->can('costs.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.costs.create') || Route::is('admin.costs.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarCost" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarCost">
                                <span class="nav-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.cost') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.costs.create') || Route::is('admin.costs.index') ? 'active' : 'collapse' }}"
                        id="sidebarCost">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('costs.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.costs.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('costs.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.costs.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   Test Report Demo's   --}}
            @if ( $userGuard->can('test_report_demos.index') || $userGuard->can('test_report_demos.create') || $userGuard->can('test_report_demos.edit') || $userGuard->can('test_report_demos.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.test_report_demos.create') || Route::is('admin.test_report_demos.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarTestReportDemo" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarTestReportDemo">
                                <span class="nav-icon">
                                    <i class="fas fa-file-medical"></i>
                                </span>
                        <span class="nav-text"> Test Report Demo's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.test_report_demos.create') || Route::is('admin.test_report_demos.index') ? 'active' : 'collapse' }}"
                        id="sidebarTestReportDemo">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.test_report_demos.create') }}">{{ __('language.create') }}</a>
                            </li>
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.test_report_demos.index') }}">{{ __('language.list') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Lab's   --}}
            @if ( $userGuard->can('labs.index') || $userGuard->can('labs.create') || $userGuard->can('labs.edit') || $userGuard->can('labs.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.labs.create') || Route::is('admin.labs.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarLab" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarLab">
                                <span class="nav-icon">
                                    <i class="fas fa-flask"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.lab') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.labs.create') || Route::is('admin.labs.index') ? 'active' : 'collapse' }}"
                        id="sidebarLab">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('labs.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.labs.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   DoctorSerial's   --}}
            @if ( $userGuard->can('doctor_serials.index') || $userGuard->can('doctor_serials.create') || $userGuard->can('doctor_serials.edit') || $userGuard->can('doctor_serials.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.doctor_serials.create') || Route::is('admin.doctor_serials.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarDoctorSerial" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarDoctorSerial">
                                <span class="nav-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </span>
                        <span class="nav-text"> Doctor Serial's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.doctor_serials.create') || Route::is('admin.doctor_serials.index') ? 'active' : 'collapse' }}"
                        id="sidebarDoctorSerial">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.doctor_serials.create') }}">{{ __('language.create') }}</a>
                            </li>
                            @if ($userGuard->can('doctor_serials.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.doctor_serials.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   DoctorRoom's   --}}
            @if ( $userGuard->can('doctor_rooms.index') || $userGuard->can('doctor_rooms.create') || $userGuard->can('doctor_rooms.edit') || $userGuard->can('doctor_rooms.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.doctor_rooms.create') || Route::is('admin.doctor_rooms.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarDoctorRoom" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarDoctorRoom">
                                <span class="nav-icon">
                                    <i class="fas fa-door-open"></i>
                                </span>
                        <span class="nav-text"> Doctor Room's </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.doctor_rooms.create') || Route::is('admin.doctor_rooms.index') ? 'active' : 'collapse' }}"
                        id="sidebarDoctorRoom">
                        <ul class="nav sub-navbar-nav">
                            <li class="sub-nav-item">
                                <a class="sub-nav-link"
                                   href="{{ route('admin.doctor_rooms.create') }}">{{ __('language.create') }}</a>
                            </li>
                            @if ($userGuard->can('doctor_rooms.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.doctor_rooms.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif

            {{--   Prescriptions   --}}
            @if ($userGuard->can('prescriptions.index') || $userGuard->can('prescriptions.create'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.prescriptions.create') || Route::is('admin.prescriptions.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPrescription" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPrescription">
            <span class="nav-icon">
                <i class="fas fa-prescription-bottle-alt"></i>
            </span>
                        <span class="nav-text"> Prescriptions </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.prescriptions.create') || Route::is('admin.prescriptions.index') ? 'active' : 'collapse' }}"
                        id="sidebarPrescription">
                        <ul class="nav sub-navbar-nav">

                            {{-- Prescriptions Create --}}
                            @if ($userGuard->can('prescriptions.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="{{ route('admin.prescriptions.create') }}">
                                        Create Prescription
                                    </a>
                                </li>
                            @endif

                            {{-- Prescriptions List --}}
                            @if ($userGuard->can('prescriptions.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="{{ route('admin.prescriptions.index') }}">
                                        Prescription List
                                    </a>
                                </li>
                            @endif
                            {{-- Prescriptions List --}}
                            @if ($userGuard->can('prescriptions.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link" href="{{ route('admin.reports.references.doctor') }}">
                                        My Earning
                                    </a>
                                </li>
                            @endif


                        </ul>
                    </div>
                </li>
            @endif

            {{--   Report's   --}}
            @if ( $userGuard->can('reports.index'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.reports.collections') || Route::is('admin.reports.references') ? 'active' : 'collapsed' }}"
                       href="#sidebarReport" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarReport">
                                <span class="nav-icon">
                                    <i class="fas fa-chart-pie"></i>
                                </span>
                        <span class="nav-text"> {{ __('language.reports') }} </span>
                    </a>
                    <div
                        class="{{ Route::is('admin.reports.collections') || Route::is('admin.reports.categories') ? 'active' : 'collapse' }}"
                        id="sidebarReport">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('reports.show'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.balance') }}">Balance</a>
                                </li>
                            @endif
                            @if ($userGuard->can('reports.show'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.collections') }}">Collections</a>
                                </li>

                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.recept-collections') }}">Hospital Collections</a>
                                </li>
                            @endif
                            @if ($userGuard->can('reports.payment'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.categories') }}">Test Report Categories</a>
                                </li>


                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.references.payment') }}">Reference Payment</a>
                                </li>
                            @endif
                            @if ($userGuard->can('reports.show'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.references') }}">Reference</a>
                                </li>


                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.reports.costs') }}">Cost</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>
            @endif


            {{--   Supplier's   --}}
            @if ( $userGuard->can('suppliers.index') || $userGuard->can('suppliers.create') || $userGuard->can('suppliers.edit') || $userGuard->can('suppliers.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.suppliers.create') || Route::is('admin.suppliers.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarSupplier" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarSupplier">
                                <span class="nav-icon">
                                    <i class="fas fa-truck"></i>
                                </span>
                        <span class="nav-text">Supplier</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.suppliers.create') || Route::is('admin.suppliers.index') ? 'active' : 'collapse' }}"
                        id="sidebarSupplier">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('suppliers.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.suppliers.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('suppliers.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.suppliers.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif



            {{--   Item's   --}}
            @if ( $userGuard->can('items.index') || $userGuard->can('items.create') || $userGuard->can('items.edit') || $userGuard->can('items.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.items.create') || Route::is('admin.items.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarItem" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarItem">
                                <span class="nav-icon">
                                    <i class="fas fa-box"></i>
                                </span>
                        <span class="nav-text">Item</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.items.create') || Route::is('admin.items.index') ? 'active' : 'collapse' }}"
                        id="sidebarItem">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('items.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.items.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('items.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.items.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


            {{--   Purchase's   --}}
            @if ( $userGuard->can('purchases.index') || $userGuard->can('purchases.create') || $userGuard->can('purchases.edit') || $userGuard->can('purchases.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.purchases.create') || Route::is('admin.purchases.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPurchase" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPurchase">
                                <span class="nav-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </span>
                        <span class="nav-text">Purchase</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.purchases.create') || Route::is('admin.purchases.index') ? 'active' : 'collapse' }}"
                        id="sidebarPurchase">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('purchases.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.purchases.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('purchases.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.purchases.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('purchases.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.items.purchases') }}">Stock items</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif



            {{--   NumberCategory's   --}}
            @if ( $userGuard->can('number_categories.index') || $userGuard->can('number_categories.create') || $userGuard->can('number_categories.edit') || $userGuard->can('number_categories.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.number_categories.create') || Route::is('admin.number_categories.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarNumberCategory" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarNumberCategory">
                                <span class="nav-icon">
                                    <i class="fas fa-list-ol"></i>
                                </span>
                        <span class="nav-text">NumberCategory</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.number_categories.create') || Route::is('admin.number_categories.index') ? 'active' : 'collapse' }}"
                        id="sidebarNumberCategory">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('number_categories.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.number_categories.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('number_categories.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.number_categories.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif




            {{--   PhoneNumber's   --}}
            @if ( $userGuard->can('phone_numbers.index') || $userGuard->can('phone_numbers.create') || $userGuard->can('phone_numbers.edit') || $userGuard->can('phone_numbers.delete'))
                <li class="nav-item">
                    <a class="nav-link menu-arrow {{ Route::is('admin.phone_numbers.create') || Route::is('admin.pone_numbers.index') ? 'active' : 'collapsed' }}"
                       href="#sidebarPhoneNumber" data-bs-toggle="collapse" role="button"
                       aria-expanded="false" aria-controls="sidebarPhoneNumber">
                                <span class="nav-icon">
                                    <i class="fas fa-phone-alt"></i>
                                </span>
                        <span class="nav-text">PhoneNumber</span>
                    </a>
                    <div
                        class="{{ Route::is('admin.phone_numbers.create') || Route::is('admin.phone_numbers.index') ? 'active' : 'collapse' }}"
                        id="sidebarPhoneNumber">
                        <ul class="nav sub-navbar-nav">
                            @if ($userGuard->can('phone_numbers.create'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.phone_numbers.create') }}">{{ __('language.create') }}</a>
                                </li>
                            @endif
                            @if ($userGuard->can('phone_numbers.index'))
                                <li class="sub-nav-item">
                                    <a class="sub-nav-link"
                                       href="{{ route('admin.phone_numbers.index') }}">{{ __('language.list') }}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif


        </ul>
    </div>
</div>
