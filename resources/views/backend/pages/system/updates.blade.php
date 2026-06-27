@extends('backend.layouts.master')
@section('title')
    {{ t('dashboard.schema_maintenance') }}
@endsection

@section('admin-content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-database me-2 text-secondary"></i>{{ t('dashboard.schema_maintenance') }}
            </h4>
            <p class="text-muted mb-0 small">{{ t('dashboard.schema_updates_page_sub') }}</p>
        </div>
        <a href="{{ route('admin.home') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ t('common.back') }}
        </a>
    </div>

    @include('backend.layouts.partials.message')

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @include('backend.layouts.partials.schema-updates-panel', [
                'schemaModules' => $schemaModules,
                'pendingCount' => $pendingCount,
            ])
        </div>
    </div>

    <div class="alert alert-light border mt-3 mb-0 small">
        <i class="fas fa-info-circle me-1 text-primary"></i>
        {{ t('dashboard.schema_safe_note') }}
    </div>
</div>
@endsection
