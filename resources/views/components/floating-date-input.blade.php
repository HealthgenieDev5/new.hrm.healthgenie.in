@props([ 'name', 'label' => '', 'value' => '', 'placeholder' => 'dd MMM YYYY', 'required' => ''])

<div class="form-floating-flatpickr">
    <div class="input-group input-group-flatpicker {{ $errors->has($name) ? 'is-invalid' : '' }}" id="{{ $name }}_picker" data-wrap="true">
        <input type="text" id="{{ $name }}" class="form-control" name="{{ $name }}" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" data-input data-open>
        <span class="input-group-text cursor-pointer" data-toggle>
            <i class="far fa-calendar-alt"></i>
        </span>
    </div>
    <label class="{{$required}} form-label">{{ $label }}</label>
</div>
<small class="text-danger d-block" id="{{ $name }}_error">{{ $errors->first($name) ?? '' }}</small>