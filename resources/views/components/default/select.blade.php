<select {{ $attributes->merge(['name' => $name, 'class' => $class, 'id' => $id ?? $name]) }}>
    <option value="">-- Choose Type --</option>
    {{ $slot }}
</select>
