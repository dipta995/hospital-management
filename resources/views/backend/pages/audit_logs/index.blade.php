@extends('backend.layouts.master')

@section('title')
    Trash History
@endsection

@section('admin-content')
    <div class="container-fluid py-3">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="mb-1">Trash History</h4>
                        <p class="text-muted mb-0">Who edited or deleted invoice, hospital receipt, and cost records.</p>
                    </div>
                </div>

                @if(isset($tableReady) && !$tableReady)
                    <div class="alert alert-warning mb-3">
                        Trash history table is not installed yet. Go to Dashboard and click <strong>Install Audit Log Table</strong>.
                    </div>
                @endif

                <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-2">
                    <div class="col-md-2">
                        <select name="module" class="form-control">
                            <option value="">All Record Types</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}" @selected(request('module') === $module)>
                                    {{ ['invoice' => 'Invoice Bill', 'recept' => 'Hospital Receipt', 'cost' => 'Cost Entry'][$module] ?? ucfirst($module) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="action" class="form-control">
                            <option value="">All Activities</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}" @selected(request('action') === $action)>
                                    {{ ['updated' => 'Edited', 'deleted' => 'Deleted'][$action] ?? ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="record_id" class="form-control" value="{{ request('record_id') }}" placeholder="Record No">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>When</th>
                        <th>Record Type</th>
                        <th>What Happened</th>
                        <th>Record No</th>
                        <th>Done By</th>
                        <th>Changes</th>
                        <th class="text-end">View</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('d M Y h:i A') }}</td>
                            <td><span class="badge bg-secondary">{{ ['invoice' => 'Invoice Bill', 'recept' => 'Hospital Receipt', 'cost' => 'Cost Entry'][$log->module] ?? ucfirst($log->module) }}</span></td>
                            <td>
                                <span class="badge {{ $log->action === 'deleted' ? 'bg-danger' : 'bg-warning text-dark' }}">
                                    {{ ['updated' => 'Edited', 'deleted' => 'Deleted'][$log->action] ?? ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>#{{ $log->auditable_id }}</td>
                            <td>{{ $log->admin->name ?? 'Unknown' }}</td>
                            <td>
                                @if($log->action === 'deleted')
                                    Full record deleted
                                @else
                                    {{ count($log->changes ?? []) }} item(s) changed
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.audit-logs.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                    See Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No trash history found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
