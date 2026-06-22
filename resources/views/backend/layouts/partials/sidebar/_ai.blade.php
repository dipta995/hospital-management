@php
    $hasAi = $userGuard->hasRole('Super Admin')
        || $userGuard->can('ai.reports')
        || $userGuard->can('ai.health')
        || $userGuard->can('ai.chat')
        || $userGuard->can('ai.analytics');
    $aiActive = Route::is('admin.ai.*');
@endphp
@if ($hasAi)
    @include('backend.layouts.partials.sidebar._section-title', [
        'title' => t('menu.ai'),
        'sectionClass' => 'section-ai',
        'sectionKey' => 'ai',
        'icon' => 'fa-brain',
    ])
    <li class="nav-item sidebar-module sidebar-module-ai" data-sidebar-section="ai">
        <a class="nav-link menu-arrow {{ $aiActive ? 'active' : 'collapsed' }}"
           href="#sidebarAiModule" data-bs-toggle="collapse" role="button"
           aria-expanded="{{ $aiActive ? 'true' : 'false' }}" aria-controls="sidebarAiModule">
            <span class="nav-icon"><i class="fas fa-wand-magic-sparkles"></i></span>
            <span class="nav-text">{{ t('menu.ai_module') }}</span>
        </a>
        <div class="{{ $aiActive ? 'collapse show' : 'collapse' }}" id="sidebarAiModule">
            <ul class="nav sub-navbar-nav sidebar-module-inner">

                <li class="sub-nav-section">{{ t('menu.ai_overview') }}</li>
                <li class="sub-nav-item">
                    <a class="sub-nav-link" href="{{ route('admin.ai.index') }}">
                        <i class="fas fa-th-large fa-fw me-1"></i> {{ t('menu.ai_hub') }}
                    </a>
                </li>

                @if ($userGuard->can('ai.analytics') || $userGuard->hasRole('Super Admin'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.ai.index') }}#insights">
                            <i class="fas fa-chart-line fa-fw me-1"></i> {{ t('menu.ai_insights') }}
                        </a>
                    </li>
                @endif

                <li class="sub-nav-section">{{ t('menu.ai_tools') }}</li>

                @if ($userGuard->can('ai.reports') || $userGuard->hasRole('Super Admin'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.invoices.index') }}">
                            <i class="fas fa-file-alt fa-fw me-1"></i> {{ t('menu.ai_report_summary') }}
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('ai.health') || $userGuard->hasRole('Super Admin'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-notes-medical fa-fw me-1"></i> {{ t('menu.ai_health') }}
                        </a>
                    </li>
                @endif

                @if ($userGuard->can('ai.chat') || $userGuard->hasRole('Super Admin'))
                    <li class="sub-nav-item">
                        <a class="sub-nav-link" href="{{ route('admin.ai.index') }}#assistant">
                            <i class="fas fa-comment-dots fa-fw me-1"></i> {{ t('menu.ai_assistant') }}
                        </a>
                    </li>
                @endif

                <li class="sub-nav-section">{{ t('menu.ai_learn') }}</li>
                <li class="sub-nav-item">
                    <a class="sub-nav-link" href="{{ route('admin.help.show', [app()->getLocale() === 'bn' ? 'bn' : 'en', 'ai']) }}">
                        <i class="fas fa-book fa-fw me-1"></i> {{ t('menu.ai_docs') }}
                    </a>
                </li>

            </ul>
        </div>
    </li>
@endif
