@php
    $fieldName = $name ?? 'cost_category_id';
    $fieldId = $id ?? 'cost_category_id';
    $fieldSelected = $selected ?? old($fieldName);
    $fieldPlaceholder = $placeholder ?? 'Search or select category...';
    $fieldRequired = $required ?? true;
@endphp

<select
    class="form-select cost-category-select"
    name="{{ $fieldName }}"
    id="{{ $fieldId }}"
    @if($fieldRequired) required @endif
    data-placeholder="{{ $fieldPlaceholder }}"
>
    <option value=""></option>
    @foreach($categories as $item)
        <option
            value="{{ $item->id }}"
            @selected((string) $fieldSelected === (string) $item->id)
        >
            [{{ ucfirst($item->type ?? 'diagnostic') }}] {{ $item->name }}
        </option>
    @endforeach
</select>
