@props([ 'name', 'label' => '', 'value' => '', 'required' => ''])

@php
$imageUrl = $value ? Storage::url($value) : '';
$defaultImage = asset('media/svg/files/blank-image.svg');
$imageBackground = $imageUrl;
$ext = pathinfo($imageUrl, PATHINFO_EXTENSION);
if ($ext == 'pdf') {
$imageBackground = asset('media/svg/files/pdf.svg');
}
@endphp

<div class="form-floating">
    <div id="{{ $name }}_select" class="image-input image-input-outline {{ $value ? '' : 'image-input-empty' }}" data-kt-image-input="true" style="background-image: url('{{ $defaultImage }}')">
        <div class="image-input-wrapper w-70px h-70px w-lg-100px h-lg-100px" style="background-image: url('{{ $imageBackground }}')">
            <div class="{{ $value ? 'd-block' : 'd-none' }} w-100 h-100 overlay preview-button">
                <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow">
                    <i class="bi bi-eye-fill text-white fs-2x cursor-pointer" data-bs-target="#{{ $name }}_lightbox" data-bs-toggle="modal"></i>
                </div>
            </div>
        </div>
        <div class="modal fade" id="{{ $name }}_lightbox" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-body d-flex" style="min-height: 70vh;">
                        <iframe id="{{ $name }}_lightbox_iframe" class="loaded_content" width="100%" src="{{ $imageUrl }}"></iframe>
                    </div>
                </div>
            </div>
        </div>

        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change attachment"
            data-bs-original-title="Change attachment">
            <i class="bi bi-pencil-fill fs-7"></i>
            <input type="file" id="{{ $name }}" name="{{ $name }}" accept=".png, .jpg, .jpeg" data-max-size="2048" />
            <input type="hidden" name="{{ $name }}_remove" />
        </label>
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel attachment"
            data-bs-original-title="Cancel attachment">
            <i class="bi bi-x fs-2"></i>
        </span>
        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove attachment"
            data-bs-original-title="Remove attachment">
            <i class="bi bi-x fs-2"></i>
        </span>
    </div>
    <label class="{{$required}} form-label text-center" for="{{ $name }}_select" style="height: max-content; padding:0px 0.5rem; font-size: 0.85rem; transform: translateY(-50%);"> {{ $label }} </label>
</div>
<small class="text-danger d-block" id="{{ $name }}_error">{{ $errors->first($name) ?? '' }}</small>