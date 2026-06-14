@php
    $hasDiagnostic = ($userGuard->can('categories.index') || $userGuard->can('categories.create'))
        || ($userGuard->can('products.index') || $userGuard->can('products.create'))
        || ($userGuard->can('invoices.index') || $userGuard->can('invoices.create'))
        || ($userGuard->can('labs.index'));
    $diagnosticActive = Route::is('admin.categories.*', 'admin.products.*', 'admin.invoices.*', 'admin.labs.*');
@endphp
@if ($hasDiagnostic)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => 'Diagnostic & Lab',
        'sectionClass' => 'section-diagnostic',
        'sectionKey' => 'diagnostic',
        'icon' => 'fa-microscope',
    ])
    <li class="nav-item sidebar-module sidebar-module-diagnostic" data-sidebar-section="diagnostic">
        <a class="nav-link menu-arrow {{ $diagnosticActive ? 'active' : 'collapsed' }}"
           href="#sidebarDiagnosticModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $diagnosticActive ? 'true' : 'false' }}" aria-controls="sidebarDiagnosticModule">
            <span class="nav-icon"><i class="fas fa-vial"></i></span>
            <span class="nav-text">Diagnostic Module</span>
        </a>
        <div class="{{ $diagnosticActive ? 'collapse show' : 'collapse' }}" id="sidebarDiagnosticModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('categories.index') || $userGuard->can('categories.create'))
                    <li class="sub-nav-section">Catalog</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-layer-group fa-fw me-1"></i> {{ __('language.category') }}
                        </a>
                    </li>
                @endif
                @if ($userGuard->can('products.index') || $userGuard->can('products.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-flask fa-fw me-1"></i> {{ __('language.test') }}
                        </a>
                    </li>
                    @if ($userGuard->can('products.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.products.create') }}">+ New Test</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('invoices.index') || $userGuard->can('invoices.create'))
                    <li class="sub-nav-section">Billing</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar fa-fw me-1"></i> {{ __('language.invoice') }} List
                        </a>
                    </li>
                    @if ($userGuard->can('invoices.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.invoices.create') }}">+ New Invoice</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('labs.index'))
                    <li class="sub-nav-section">Lab & Reports</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.labs.index') }}">
                            <i class="fas fa-flask fa-fw me-1"></i> {{ __('language.lab') }}
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
