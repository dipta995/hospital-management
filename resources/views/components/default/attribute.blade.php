<a {{ $attributes->merge(['class' => 'badge '.$class,'href' => $href]) }}>
    {{ $slot ?? 'create' }}
</a>
