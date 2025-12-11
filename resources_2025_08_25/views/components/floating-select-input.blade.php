@props([ 'name', 'label' => '', 'options' => [], 'value' => null, 'placeholder' => 'Select an option', 'required' => '', 'class' => ''])

@php
$isMultiple = $attributes->has('multiple');

// Normalize the selected value
$selectedValues = old($name, $value);

// Handle both string and array
if ($isMultiple) {
$selectedValues = is_array($selectedValues) ? $selectedValues : (array) $selectedValues;

// If 'all' is selected, override everything else
if (in_array('all', $selectedValues)) {
$selectedValues = ['all'];
}
} else {
$selectedValues = $selectedValues ?? 'all';
}



$isSelected = function ($id) use ($selectedValues, $isMultiple) {
return $isMultiple
? in_array($id, $selectedValues)
: $id == $selectedValues;
};

@endphp

<div class="form-floating">
    <select
        name="{{ $isMultiple ? $name.'[]' : $name }}"
        id="{{ $name }}"
        class="form-select {{ $class }} {{ $errors->has($name) ? 'is-invalid' : '' }}"
        data-control="select2"
        data-placeholder="{{ $placeholder }}"
        {{ $attributes }}>




        <option value="{{ $attributes['include_blank'] ? 'all' : '' }}" {{ $isSelected('all') ? 'selected' : '' }}>
            All {{ $label }}
        </option>

        @foreach($options as $option)
        <option value="{{ $option->id }}" {{ $isSelected($option->id) ? 'selected' : '' }}>
            {{ $attributes['select_for'] && $attributes['select_for'] == 'worker'
                    ? $option->name . ' (' . $option->code . ')'
                    : $option->name }}
        </option>


        @endforeach
    </select>
    <label for="{{ $name }}" class="{{$required}} form-label">{{ $label }}</label>
</div>
<small class="text-danger d-block" id="{{ $name }}_error">{{ $errors->first($name) ?? '' }}</small>