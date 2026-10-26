@props([ 'name', 'label' => '', 'value' => '', 'type' => 'text', 'required' => ''])

<div class="form-floating">
    <textarea type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="" {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }} style="height: 100px">{{ old($name, $value) }}</textarea>
    <label for="{{ $name }}" class="{{$required}} form-label">{{ $label }}</label>
</div>
<small class="text-danger d-block" id="{{ $name }}_error">{{ $errors->first($name) ?? '' }}</small>