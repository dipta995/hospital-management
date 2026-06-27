@php
    $hasDiagnostic = ($userGuard->can('categories.index') || $userGuard->can('categories.create'))
        || ($userGuard->can('products.index') || $userGuard->can('products.create'))
        || ($userGuard->can('invoices.index') || $userGuard->can('invoices.create'))
        || ($userGuard->can('labs.index'));
    $diagnosticActive = Route::is('admin.categories.*', 'admin.products.*', 'admin.invoices.*', 'admin.labs.*', 'admin.labs.tests');
@endphp
@if ($hasDiagnostic)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.diagnostic_lab'),
        'sectionClass' => 'section-diagnostic',
        'sectionKey' => 'diagnostic',
        'icon' => 'fa-microscope',
    ])
    <li class="nav-item sidebar-module sidebar-module-diagnostic" data-sidebar-section="diagnostic">
        <a class="nav-link menu-arrow {{ $diagnosticActive ? 'active' : 'collapsed' }}"
           href="#sidebarDiagnosticModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $diagnosticActive ? 'true' : 'false' }}" aria-controls="sidebarDiagnosticModule">
            <span class="nav-icon"><i class="fas fa-vial"></i></span>
            <span class="nav-text">{{ t('menu.diagnostic_module') }}</span>
        </a>
        <div class="{{ $diagnosticActive ? 'collapse show' : 'collapse' }}" id="sidebarDiagnosticModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                @if ($userGuard->can('categories.index') || $userGuard->can('categories.create'))
                    <li class="sub-nav-section">{{ t('menu.catalog') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-layer-group fa-fw me-1"></i> {{ t('category') }}
                        </a>
                    </li>
                @endif
                @if ($userGuard->can('products.index') || $userGuard->can('products.create'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-flask fa-fw me-1"></i> {{ t('test') }}
                        </a>
                    </li>
                    @if ($userGuard->can('products.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.products.create') }}">+ {{ t('menu.new_test') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('invoices.index') || $userGuard->can('invoices.create'))
                    <li class="sub-nav-section">{{ t('menu.billing') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.invoices.index') }}">
                            <i class="fas fa-file-invoice-dollar fa-fw me-1"></i> {{ t('menu.invoice_list') }}
                        </a>
                    </li>
                    @if ($userGuard->can('invoices.create'))
                        <li class="sub-nav-item">
                            <a class="sub-nav-link" href="{{ route('admin.invoices.create') }}">+ {{ t('menu.new_invoice') }}</a>
                        </li>
                    @endif
                @endif

                @if ($userGuard->can('labs.index'))
                    <li class="sub-nav-section">{{ t('menu.lab_reports') }}</li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.labs.index') }}">
                            <i class="fas fa-flask fa-fw me-1"></i> {{ t('menu.lab_queue') }}
                        </a>
                    </li>
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.labs.tests') }}">
                            <i class="fas fa-vial fa-fw me-1"></i> {{ t('menu.lab_tests') }}
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </li>
@endif
