@php
    $dotPrefix = str_replace(['[', ']'], ['.', ''], $prefix);
@endphp

@php
    $getValue = fn($field) => old("{$dotPrefix}.{$field}", $existingRequirement[$prefix][$field] ?? 0);
@endphp

<div class="form-group mb-3">
    <label>Workers</label>
    <input type="number" min="0" name="{{ $prefix }}[workers]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.workers') is-invalid @enderror"
        value="{{ $getValue('workers') }}" data-shift="{{ $prefix }}" required readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.workers') }}
    </small>
</div>

<div class="form-group mb-3">
    <label>Extras</label>
    <input type="number" min="0" name="{{ $prefix }}[extras]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.extras') is-invalid @enderror"
        value="{{ $getValue('extras') }}" data-shift="{{ $prefix }}" readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.extras') }}
    </small>
</div>

<div class="form-group mb-3">
    <label>Supervisors</label>
    <input type="number" min="0" name="{{ $prefix }}[supervisors]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.supervisors') is-invalid @enderror"
        value="{{ $getValue('supervisors') }}" data-shift="{{ $prefix }}" readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.supervisors') }}
    </small>
</div>

<div class="form-group mb-3">
    <label>QAs</label>
    <input type="number" min="0" name="{{ $prefix }}[qas]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.qas') is-invalid @enderror"
        value="{{ $getValue('qas') }}" data-shift="{{ $prefix }}" readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.qas') }}
    </small>
</div>

<div class="form-group mb-3">
    <label>Services</label>
    <input type="number" min="0" name="{{ $prefix }}[services]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.services') is-invalid @enderror"
        value="{{ $getValue('services') }}" data-shift="{{ $prefix }}" readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.services') }}
    </small>
</div>

<div class="form-group mb-3">
    <label>Movers</label>
    <input type="number" min="0" name="{{ $prefix }}[movers]"
        class="shift-input form-control form-control-sm @error($dotPrefix . '.movers') is-invalid @enderror"
        value="{{ $getValue('movers') }}" data-shift="{{ $prefix }}" readonly>
    <small class="text-danger d-block">
        {{ $errors->first($dotPrefix . '.movers') }}
    </small>
</div>
