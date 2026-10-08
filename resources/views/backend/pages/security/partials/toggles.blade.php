@foreach($toggles as $toggle)
    <div class="d-flex justify-content-between align-items-start gap-3 border-top py-2">
        <div>
            <label class="fw-semibold mb-0" for="{{ $toggle['name'] }}">{{ $toggle['label'] }}</label>
            <div class="small text-muted">{{ $toggle['help'] }}</div>
        </div>
        <select name="{{ $toggle['name'] }}" id="{{ $toggle['name'] }}" class="form-select form-select-sm" style="width: 90px;">
            @foreach(['Yes' => 'চালু', 'No' => 'বন্ধ'] as $value => $text)
                <option value="{{ $value }}" @selected(old($toggle['name'], $toggle['value']) === $value)>{{ $text }}</option>
            @endforeach
        </select>
    </div>
@endforeach
