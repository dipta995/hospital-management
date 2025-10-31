<input {{ $attributes->merge(['name' => $name,'class' => $class,'id' => $id ?? $name ,'type' => $type,'value' => old($name,$data ?? null)]) }}>
