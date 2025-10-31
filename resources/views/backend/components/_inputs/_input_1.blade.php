@if ($input['html_type'] == 'select')
    @php
        $viewindex = $input['show_fields'];
    @endphp
    <div class="form-group">
        <label for="basicInput">{{ str_replace('_', ' ', strtoupper($input['title'])) }}</label>
        <select name="{{ isset($input['multiple']) ? $input['name'] . '[]' : $input['name'] }}"
            {{ isset($input['required']) ? 'required' : '' }}
            class="form-control simple-selector {{ isset($input['update']) ? $input['name'] : '' }} {{ isset($input['multiple']) ? 'choices multiple-remove' : '' }} @error($input['name']) is-invalid @enderror"
            id="{{ isset($input['update']) ? '' : $input['name'] }}" {{ isset($input['multiple']) ? 'multiple' : '' }}>
            {{--        @if (isset($input['multiple'])) --}}
            {{--        @else --}}
            {{--        <option value=""> --CHOOSE {{ str_replace( '_',' ', strtoupper($input['title'])) }} --</option> --}}
            {{--        @endif --}}

            @foreach (getData($input['modelData']) as $item)
                <option value="{{ $item->id }}">{{ $item->$viewindex }}</option>
            @endforeach
            @error($input['name'])
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </select>
    </div>
@endif
@if ($input['html_type'] == 'input')
    <div class="form-group">
        @if ($input['name'] != 'id')
            <label for="basicInput">{{ str_replace('_', ' ', strtoupper($input['title'])) }}</label>
        @endif
        <input type="{{ $input['type'] }}" name="{{ $input['name'] }}"
            {{ isset($input['required']) ? 'required' : '' }} {{ isset($input['min']) ? 'min=' . $input['min'] : '' }}
            {{ isset($input['max']) ? 'max=' . $input['max'] : '' }}
            class="{{ $input['type'] == 'radio' || $input['type'] == 'checkbox' ? 'form-check-input' : 'form-control' }}  {{ isset($input['update']) ? $input['name'] : '' }}"
            id="{{ isset($input['update']) ? '' : $input['name'] }} @error($input['name']) is-invalid @enderror"
            placeholder="{{ isset($input['update']) ? '' : $input['placeholder'] }}">
        @error($input['name'])
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    </div>
@endif
