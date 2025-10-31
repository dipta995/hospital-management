
<div class="form-group">
    <label for="basicInput">{{ $input['name'] }}</label>
    <select name="{{ $input['name'] }}" {{ isset($input['required']) ? "required" : "" }} class="form-control {{  isset($input['update']) ? $input['name'] : '' }}" id="{{ isset($input['update']) ? '' : $input['name'] }}">
        @foreach ($input['modelData'] as $item)
        <option value="{{ $item->id }}" >{{ $item->{$input->['view_index']} }}</option>
        @endforeach
    </select>
</div>

