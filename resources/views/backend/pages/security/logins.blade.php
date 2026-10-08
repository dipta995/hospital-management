@extends('backend.layouts.master')

@section('title')
    Login History
@endsection

@section('admin-content')
    <div class="container-fluid py-3">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h4 class="mb-1"><i class="fas fa-right-to-bracket text-primary"></i> Login History</h4>
                <p class="text-muted">কে কখন, কোন IP ও device থেকে login করেছে, আর কতবার ভুল password দিয়েছে। ৫ বার ভুল দিলে ৫ মিনিট login বন্ধ থাকে। ১ মাসের পুরনো রেকর্ড কেউ login করার সময় নিজে থেকে মুছে যায়।</p>

                @if(!$tableReady)
                    <div class="alert alert-warning mb-0">Security tables এখনো install করা হয়নি। Support-এ যোগাযোগ করুন।</div>
                @else
                    <form method="GET" class="row g-2">
                        <div class="col-md-3">
                            <select name="event" class="form-control">
                                <option value="">All Events</option>
                                @foreach(['login' => 'Login', 'logout' => 'Logout', 'failed' => 'Wrong Password', 'locked' => 'Locked (too many tries)'] as $value => $label)
                                    <option value="{{ $value }}" @selected(request('event') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="admin_id" class="form-control">
                                <option value="">Anyone</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" @selected((string) request('admin_id') === (string) $admin->id)>{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button class="btn btn-primary flex-fill">Filter</button>
                            <a href="{{ route('admin.security.logins') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        @if($tableReady)
            <div class="card border-0 shadow-sm">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                        <tr>
                            <th>When</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>IP</th>
                            <th>Device</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($logins as $item)
                            @php
                                $eventBadge = ['login' => 'bg-success', 'logout' => 'bg-secondary', 'failed' => 'bg-danger', 'locked' => 'bg-dark'][$item->event] ?? 'bg-light text-dark';
                                $eventText = ['login' => 'Login', 'logout' => 'Logout', 'failed' => 'Wrong Password', 'locked' => 'Locked'][$item->event] ?? $item->event;
                            @endphp
                            <tr>
                                <td>{{ $item->created_at?->timezone('Asia/Dhaka')->format('d M Y h:i:s A') }}</td>
                                <td>{{ $item->admin->name ?? '—' }}<div class="small text-muted">{{ $item->email }}</div></td>
                                <td><span class="badge {{ $eventBadge }}">{{ $eventText }}</span></td>
                                <td><code>{{ $item->ip_address }}</code></td>
                                <td class="small text-muted" style="max-width: 360px;">{{ \Illuminate\Support\Str::limit($item->user_agent, 90) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">কোনো রেকর্ড নেই।</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    {{ $logins->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
