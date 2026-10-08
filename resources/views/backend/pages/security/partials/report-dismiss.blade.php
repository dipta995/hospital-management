@php
    $keys = (array) $keys;
    $isHidden = collect($keys)->every(fn ($key) => isset($dismissed[$key]));
    $isCleared = !$isHidden && collect($keys)->every(fn ($key) => isset($hiddenKeys[$key]));
@endphp
@if($isCleared)
    <span class="badge bg-light text-muted border" title="পুরো report বা staff clear করার কারণে লুকানো। উপরের Undo দিয়ে ফেরানো যায়।">Clear করা</span>
@else
<form method="POST" action="{{ route('admin.security.report.dismiss') }}" class="d-inline"
      @unless($isHidden) onsubmit="return confirm('{{ $confirm ?? 'এটা report থেকে সরাবেন? Trash/Audit-এর রেকর্ড মুছবে না, পরে Restore করা যাবে।' }}')" @endunless>
    @csrf
    @foreach($keys as $key)
        <input type="hidden" name="keys[]" value="{{ $key }}">
    @endforeach
    <input type="hidden" name="mode" value="{{ $isHidden ? 'restore' : 'hide' }}">
    @if($isHidden)
        <button type="submit" class="btn btn-sm btn-outline-success {{ $class ?? '' }}" title="আবার report-এ দেখান">
            <i class="fas fa-rotate-left"></i>{{ isset($label) ? ' Restore' : '' }}
        </button>
    @else
        <button type="submit" class="btn btn-sm btn-outline-secondary {{ $class ?? '' }}" title="Report থেকে সরান">
            <i class="fas fa-xmark"></i>{{ $label ?? '' }}
        </button>
    @endif
</form>
@endif
