@props([
'name',
'label' => '',
'value' => '',
'type' => 'text',
'required' => '',
'placeholder' => '',
'icon' => '',
'numeric' => false,
'decimal' => false
])


<div class="input-group d-flex {{ $errors->has($name) ? 'is-invalid' : '' }}" id="{{ $name }}">
    <div class="form-floating flex-grow-1">
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder ?? $label }}"
            {{
                $attributes->merge(
                    [
                        'class' => 'form-control ' . ($icon ? 'form-control-sm rounded-0 rounded-start' : '') . ($errors->has($name) ? 'is-invalid' : ''),
                        'data-numeric' => $numeric ? 'true' : 'false',
                        'data-decimal' => $decimal ? 'true' : 'false'
                    ]
                )
            }} />
        <label for="{{ $name }}" class="{{$required}} form-label">{{ $label }}</label>
    </div>
    @if($icon)
    <span class="input-group-text">{{ $icon }}</span>
    @endif
</div>


<small class="text-danger d-block" id="{{ $name }}_error">
    {{ $errors->first($name) ?? '' }}
</small>