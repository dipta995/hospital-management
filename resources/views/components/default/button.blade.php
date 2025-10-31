<button {{ $attributes->merge(['class' => 'btn '.$class,'type' => $type ?? 'submit']) }}>
    {{ $slot }}
</button>
