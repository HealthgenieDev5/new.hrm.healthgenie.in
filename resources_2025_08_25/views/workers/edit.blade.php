<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>


    <x-slot name="headerComponents">
        <button id="revision_drawer_trigger" class="btn btn-link btn-sm">Revisions</button>
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="edit_worker" class="card bg-transparent mx-auto" enctype="multipart/form-data" method="POST"
                action="{{ route('workers.update', $worker->id)}}" style="max-width: 768px">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body p-0 p-md-7">
                    <div class="row gy-5">

                        <div class="col-8 d-flex flex-column gap-7">
                            <div>
                                <div class="form-floating">
                                    <select name="psp_id" id="psp_id" class="form-select {{$errors->has('psp_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select PSP">
                                        <option></option>
                                        @foreach($psps as $psp)
                                        <option value="{{ $psp->id }}" {{ old('psp_id', $worker->psp_id) == $psp->id ? 'selected' : '' }}>
                                            {{ $psp->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="psp_id" class="required form-label">PSP</label>
                                    <small class="text-danger d-block" id="name_error">{{ $errors->first('psp_id') ?? '' }}</small>
                                </div>
                            </div>
                            <div>
                                <div class="form-floating">
                                    <input type="text" class="form-control {{$errors->has('name') ? 'is-invalid' : '' }}" name="name" id="name" value="{{ old('name', $worker->name) }}" placeholder="" />
                                    <label for="name" class="required form-label">Worker Name</label>
                                </div>
                                <small class="text-danger d-block" id="name_error">{{ $errors->first('name') ?? '' }}</small>
                            </div>
                            <div>
                                <div class="form-floating">
                                    <input type="text" class="form-control {{$errors->has('code') ? 'is-invalid' : '' }}" name="code" id="code" value="{{ old('code', $worker->code) }}" placeholder="" />
                                    <label for="code" class="required form-label">Worker code</label>
                                </div>
                                <small class="text-danger d-block" id="code_error">{{ $errors->first('code') ?? '' }}</small>
                            </div>
                            <div>
                                <div class="form-floating-flatpickr">
                                    <div class="input-group input-group-flatpicker {{$errors->has('date_of_joining') ? 'is-invalid' : '' }}" id="date_of_joining_picker" data-wrap="true">
                                        <input type="text" id="date_of_joining" class="form-control" name="date_of_joining" value="{{ old('date_of_joining', $worker->date_of_joining) }}" placeholder="joining Date" data-input data-open>
                                        <span class="input-group-text cursor-pointer" data-toggle>
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <label class="required form-label">Joining Date</label>
                                </div>
                                <small class="text-danger d-block" id="code_error">{{ $errors->first('date_of_joining') ?? '' }}</small>
                            </div>

                            <div>
                                <div class="form-floating">
                                    <select name="employment_status" id="employment_status" class="form-select {{$errors->has('employment_status') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Status">
                                        <option></option>
                                        <option value="Active" {{ old('employment_status', $worker->employment_status) == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Left" {{ old('employment_status', $worker->employment_status) == 'Left' ? 'selected' : '' }}>Left</option>
                                        <option value="Terminated" {{ old('employment_status', $worker->employment_status) == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                                        <option value="Absconded" {{ old('employment_status', $worker->employment_status) == 'Absconded' ? 'selected' : '' }}>Absconded</option>
                                        <option value="Retired" {{ old('employment_status', $worker->employment_status) == 'Retired' ? 'selected' : '' }}>Retired</option>
                                        <option value="Under Resignation" {{ old('employment_status', $worker->employment_status) == 'Under Resignation' ? 'selected' : '' }}>Under Resignation</option>
                                    </select>
                                    <label for="employment_status" class="required form-label">Status</label>
                                    <small class="text-danger d-block" id="employment_status_error">{{ $errors->first('employment_status') ?? '' }}</small>
                                </div>
                            </div>

                            <div>
                                <div class="form-floating-flatpickr">
                                    <div class="input-group input-group-flatpicker {{$errors->has('date_of_leaving') ? 'is-invalid' : '' }}" id="date_of_leaving_picker" data-wrap="true">
                                        <input type="text" id="date_of_leaving" class="form-control" name="date_of_leaving" value="{{ old('date_of_leaving', $worker->date_of_leaving) }}" placeholder="Date of leaving" data-input data-open>
                                        <span class="input-group-text cursor-pointer" data-toggle>
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <label class="required form-label">Date of Leaving</label>
                                </div>
                                <small class="text-danger d-block" id="date_of_leaving_error">{{ $errors->first('date_of_leaving') ?? '' }}</small>
                            </div>


                            <div>
                                <div class="form-floating">
                                    <input type="text" class="form-control {{$errors->has('father_name') ? 'is-invalid' : '' }}" name="father_name" id="father_name" value="{{ old('father_name', $worker->father_name) }}" placeholder="" />
                                    <label for="father_name" class="required form-label">Father's name</label>
                                </div>
                                <small class="text-danger d-block" id="father_name_error">{{ $errors->first('father_name') ?? '' }}</small>
                            </div>
                            <!-- <div>
                                <div class="form-floating">
                                    <input type="text" class="form-control {{$errors->has('husband_name') ? 'is-invalid' : '' }}" name="husband_name" id="husband_name" value="{{ old('husband_name') }}" placeholder="" />
                                    <label for="husband_name" class="required form-label">Husband's name</label>
                                </div>
                                <small class="text-danger d-block" id="husband_name_error">{{ $errors->first('husband_name') ?? '' }}</small>
                            </div> -->
                            <div>
                                <div class="form-floating">
                                    <select name="shift_id" id="shift_id" class="form-select {{$errors->has('shift_id') ? 'is-invalid' : '' }}" data-control="select2"
                                        data-placeholder="Select Shift">
                                        <option></option>
                                        @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}" {{ old('shift_id', $worker->shift_id ?? $shift->id) == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="shift_id" class="required form-label">Shift</label>
                                </div>
                                <small class="text-danger d-block" id="shift_id_error">{{ $errors->first('shift_id') ?? '' }}</small>
                            </div>





                            <div>
                                <div class="form-floating">
                                    <input type="text" name="mobile_number" value="{{ old('mobile_number', $worker->mobile_number) }}"
                                        class="form-control {{$errors->has('mobile_number') ? 'is-invalid' : '' }}" placeholder="Mobile number" />
                                    <label class="required form-label">Mobile number</label>
                                </div>
                                <small class="text-danger d-block" id="mobile_number_error">{{ $errors->first('mobile_number') ?? '' }}</small>
                            </div>
                            <div>
                                <div class="form-floating">
                                    <select name="gender" id="gender" class="form-select {{$errors->has('gender') ? 'is-invalid' : '' }}" data-control="select2"
                                        data-placeholder="Select Gender">
                                        <option></option>
                                        <option value="male" {{ old('gender', $worker->gender ?? 'male') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $worker->gender ?? 'female') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $worker->gender ?? 'other') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <label for="gender" class="required form-label">Gender</label>
                                </div>
                                <small class="text-danger d-block" id="gender_error">{{ $errors->first('gender') ?? '' }}</small>
                            </div>
                            <div>
                                <div class="form-floating">
                                    <input type="text" name="adhar_number" id="adhar_number" value="{{ old('adhar_number', $worker->adhar_number) }}"
                                        class="form-control {{$errors->has('adhar_number') ? 'is-invalid' : '' }}" placeholder="Adhar number" />
                                    <label for="adhar_number" class="required form-label">Adhar number</label>
                                </div>
                                <small class="text-danger d-block" id="adhar_number_error">{{ $errors->first('adhar_number') ?? '' }}</small>
                            </div>
                        </div>

                        <div class="col-4 d-flex flex-column align-items-center gap-7">
                            <div class="d-flex flex-column align-items-center mb-3">
                                <label class="required form-label text-center" for="photo_select">Photo</label><br>
                                <div id="photo_select" class="image-input image-input-outline <?php echo $worker->photo ? '' : 'image-input-empty'; ?>" data-kt-image-input="true" style="background-image: url(<?= asset('media/avatars/blank.png') ?>)">
                                    @php
                                    $blank_worker_photo = "";
                                    @endphp
                                    <div class="image-input-wrapper w-70px h-70px w-lg-100px h-lg-100px"
                                        style="background-image:url('{{$worker->photo ? Storage::url($worker->photo) : $blank_worker_photo}}')">
                                    </div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Change attachment">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <input type="file" id="photo" name="photo" accept=".png, .jpg, .jpeg" data-max-size="2048" />
                                        <input type="hidden" name="photo_remove" />
                                        <input type="hidden" id="photoExists" value="{{ $worker->photo ? 1 : 0 }}">
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Cancel attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Remove attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                </div>
                                <br>
                                <small class="text-danger d-block text-center" id="photo_error">{{ $errors->first('photo') ?? '' }}</small>
                            </div>

                            <div class="d-flex flex-column align-items-center  mb-3">
                                <label class="required form-label  text-center" for="adhar_front_select">Adhar front</label><br>
                                <div id="adhar_front_select" class="image-input image-input-outline  {{ $worker->adhar_front ? '' : 'image-input-empty' }}" data-kt-image-input="true" style="background-image: url(<?= asset('media/svg/files/blank-image.svg') ?>)">
                                    @php
                                    $blank_worker_adhar_front = "";
                                    @endphp
                                    <div class="image-input-wrapper w-70px h-70px w-lg-100px h-lg-100px" style="background-image:url('{{$worker->adhar_front ? Storage::url($worker->adhar_front) : $blank_worker_adhar_front}}')"> </div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Change attachment">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <input type="file" id="adhar_front" name="adhar_front" accept=".png, .jpg, .jpeg" data-max-size="2048" />
                                        <input type="hidden" name="adhar_front_remove" />
                                        <input type="hidden" id="adhar_frontExists" value="{{ $worker->adhar_front ? 1 : 0 }}">
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Cancel attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Remove attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                </div>
                                <br>
                                <small class="text-danger d-block text-center" id="adhar_front_error">{{ $errors->first('adhar_front') ?? '' }}</small>
                            </div>

                            <div class="d-flex flex-column align-items-center  mb-3">
                                <label class="required form-label  text-center" for="adhar_back_select">Adhar back</label><br>
                                <div id="adhar_back_select" class="image-input image-input-outline {{ $worker->adhar_back ? '' : 'image-input-empty' }}" data-kt-image-input="true" style="background-image: url(<?= asset('media/svg/files/blank-image.svg') ?>)">
                                    @php
                                    $blank_worker_adhar_back = "";
                                    @endphp
                                    <div class="image-input-wrapper w-70px h-70px w-lg-100px h-lg-100px" style="background-image:url('{{$worker->adhar_back ? Storage::url($worker->adhar_back) : $blank_worker_adhar_back}}')"></div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Change attachment">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <input type="file" id="adhar_back" name="adhar_back" accept=".png, .jpg, .jpeg" data-max-size="2048" />
                                        <input type="hidden" name="adhar_back_remove" />
                                        <input type="hidden" id="adhar_backExists" value="{{ $worker->adhar_back ? 1 : 0 }}">
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Cancel attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title=""
                                        data-bs-original-title="Remove attachment">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                </div>
                                <br>
                                <small class="text-danger d-block text-center" id="adhar_back_error">{{ $errors->first('adhar_back') ?? '' }}</small>
                            </div>
                        </div>


                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger btn-sm ">Update</button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <!--begin::Revision Drawer-->
    <div
        id="revision_drawer"
        class="bg-white"
        data-kt-drawer="true"
        data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#revision_drawer_trigger"
        data-kt-drawer-close="#revision_drawer_close"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'300px', 'md': '500px'}">

        <div class="card rounded-0 w-100">
            <div class="card-header pe-5">
                <div class="card-title">
                    <div class="d-flex justify-content-center flex-column me-3">
                        <a href="#" class="fs-4 fw-bolder text-gray-900 text-hover-primary me-1 lh-1">Revisions</a>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="revision_drawer_close">
                        <i class="fa fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="card-body hover-scroll-overlay-y">
                @if($revisions->isNotEmpty())
                @foreach ($revisions as $revision)
                @php
                $oldValues = json_decode($revision->old_values, true) ?? [];
                $newValues = json_decode($revision->new_values, true) ?? [];
                $fields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                @endphp
                <div style="border:1px solid #ddd; margin-bottom:1rem; padding:0.5rem;">
                    <div><strong>Revision #{{ $revision->id }}</strong></div>
                    <div>Changed at: {{ $revision->created_at }}</div>
                    <div>Changed by User ID: {{ $revision->user->name }}</div>
                    <ul>
                        @forelse($fields as $field)
                        <li>
                            <strong>{{ $field }}</strong><br>

                            Old: {{ isset($oldValues[$field]) && !empty($oldValues[$field]) ? $oldValues[$field] : 'N/A' }}<br>
                            New: {{ $newValues[$field] ?? 'N/A' }}

                        </li>
                        @empty
                        <li>No fields changed in this revision.</li>
                        @endforelse
                    </ul>

                </div>
                @endforeach
                @else
                <p>No revisions found.</p>
                @endif
            </div>
        </div>
    </div>
    <!--end::Revision Drawer-->


    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#date_of_joining_picker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    altInputClass: "form-control"
                });
                $('#date_of_leaving_picker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    altInputClass: "form-control",
                    maxDate: "<?php echo date('Y-m-d'); ?>"
                });
            })

            $(document).ready(function() {

                $('.image-input').each(function() {
                    var id = $(this).attr('id');
                    var imageInputElement = document.querySelector("#" + id);
                    var imageInput = KTImageInput.getInstance(imageInputElement);

                    imageInput.on("kt.imageinput.changed", function() {
                        var fileInput = $(imageInputElement).find("input[type=file]")[0];
                        var file = $(fileInput.files)[0];
                        var imageInputWrapper = $(imageInputElement).find(".image-input-wrapper");
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var extension = file.name.split('.').pop().toLowerCase();
                            switch (extension) {
                                case 'pdf':
                                    imageInputWrapper.css({
                                        'background-image': "url(<?= asset('media/svg/files/pdf.svg') ?>)"
                                    });
                                    break;
                                case 'zip':
                                    imageInputWrapper.css({
                                        'background-image': "url(<?= asset('media/svg/files/zip-file-icon.svg') ?>)"
                                    });
                                    break;
                                default:
                                    break;
                            }
                        }
                        reader.readAsDataURL(file);

                        var validated = validateFileInput(fileInput);
                        if (!validated) {
                            removeImageOnValidationFail(imageInputElement);
                        }
                    });
                });

                // Validate function
                function validateFileInput(input) {
                    var $input = $(input);
                    var files = $input[0].files;
                    if (files.length > 0) {
                        var file = files[0];
                        var filename = file.name;
                        var allowedTypes = $input.attr("accept") || "";
                        allowedTypes = allowedTypes.replace(/ /g, "");
                        var maxSize = $input.data("max-size") || 2048;
                        maxSize = maxSize * 1024;
                        var allowedExtensionsRegx = new RegExp("(" + allowedTypes.replace(/,/g, "|") + ")$", "i");
                        var extension = filename.substr(filename.lastIndexOf("."));
                        var isAllowed = allowedExtensionsRegx.test(extension);
                        if (!isAllowed) {
                            alert("Invalid file type. Allowed types: " + allowedTypes.replace(/,/g, ", "));
                            return false
                        }
                        if (file.size > maxSize) {
                            alert("File size exceeds the maximum limit of " + maxSize / 1024 + " KB.");
                            return false
                        }
                    } else {
                        alert("No file selected.");
                        return false;
                    }
                    return true;
                }

                // Clear after validation failed
                function removeImageOnValidationFail(imageInputElement) {
                    var cancelAction = imageInputElement.querySelector('[data-kt-image-input-action="cancel"]');
                    if (cancelAction) {
                        cancelAction.click();
                    } else {
                        console.error("Cancel action not found for the image input element.");
                    }
                }

                $(document).on('input', '.form-control', function() {
                    let nameAttr = $(this).attr('name');
                    $(this).removeClass("is-invalid");
                    $('#' + nameAttr + '_error').html("");
                });

                $(document).on('change', '.form-select[data-control="select2"]', function() {
                    let nameAttr = $(this).attr('name');
                    $(this).parent().find('.select2-selection').removeClass("is-invalid");
                    $('#' + nameAttr + '_error').html("");
                });

                $(document).on('change', '.image-input input[type="file"]', function() {
                    let nameAttr = $(this).attr('name');
                    console.log(nameAttr);
                    $(this).parent().parent().find('.image-input-wrapper').removeClass("is-invalid");
                    $('#' + nameAttr + '_error').html("");
                });

                $(document).on('change', '.form-floating-flatpickr input', function() {
                    let nameAttr = $(this).attr('name');
                    console.log(nameAttr);
                    $(this).next().removeClass("is-invalid");
                    $('#' + nameAttr + '_error').html("");
                });

                $(document).on('submit', '#edit_worker', function(e) {
                    var photoInput = $('#photo')[0];
                    var adhar_frontInput = $('#adhar_front')[0];
                    var adhar_backInput = $('#adhar_back')[0];

                    var photoExists = $('input#photoExists');
                    var photo_remove = $('input[name="photo_remove"]');
                    var adhar_frontExists = $('input#adhar_frontExists');
                    var adhar_front_remove = $('input[name="adhar_front_remove"]');
                    var adhar_backExists = $('input#adhar_backExists');
                    var adhar_back_remove = $('input[name="adhar_back_remove"]');

                    // console.log(adhar_backExists.val());
                    // return false;

                    console.log("adhar_back_remove", adhar_back_remove.val());
                    console.log("adhar_frontInput.files.length", adhar_frontInput.files.length);

                    var name = $('input[name="name"]');
                    var father_name = $('input[name="father_name"]');
                    var husband_name = $('input[name="husband_name"]');

                    var shift_id = $('select[name="shift_id"]');
                    var psp_id = $('select[name="psp_id"]');
                    var code = $('input[name="code"]');

                    var gender = $('select[name="gender"]');
                    var marital_status = $('select[name="marital_status"]');
                    var mobile_number = $('input[name="mobile_number"]');
                    var adhar_number = $('input[name="adhar_number"]');
                    var form_validated = true;


                    // if (
                    //     (!photoInput.files || photoInput.files.length === 0) && (photo_remove.val() == 1 || photoExists.val() == 0)
                    // ) {
                    //     $("#photo_error").html("Please select a photo before submitting.");
                    //     $(photoInput).parent().parent().find('.image-input-wrapper').addClass('is-invalid');
                    //     form_validated = false;
                    //     e.preventDefault();
                    // } else {
                    //     $("#photo_error").html("");
                    // }

                    // if (
                    //     (!adhar_frontInput.files || adhar_frontInput.files.length === 0) && (adhar_front_remove.val() == 1 || adhar_frontExists.val() == 0)
                    // ) {
                    //     $("#adhar_front_error").html("Please select a adhar front photo before submitting.");
                    //     $(adhar_frontInput).parent().parent().find('.image-input-wrapper').addClass('is-invalid');
                    //     form_validated = false;
                    //     e.preventDefault();
                    // } else {
                    //     $("#adhar_front_error").html("");
                    // }

                    // if (
                    //     (!adhar_backInput.files || adhar_backInput.files.length === 0) && (adhar_back_remove.val() == 1 || adhar_backExists.val() == 0)
                    // ) {
                    //     $("#adhar_back_error").html("Please select a adhar back photo before submitting.");
                    //     $(adhar_backInput).parent().parent().find('.image-input-wrapper').addClass('is-invalid');
                    //     form_validated = false;
                    //     e.preventDefault();
                    // } else {
                    //     $("#adhar_back_error").html("");
                    // }


                    if (!name.val() || name.val().length === 0) {
                        $("#name_error").html("Please Enter name before submitting.");
                        $(name).addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#name_error").html("");
                    }
                    if (!father_name.val() || father_name.val().length === 0) {
                        $("#father_name_error").html("Please Enter Father's name before submitting.");
                        $(father_name).addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#father_name_error").html("");
                    }

                    if (!shift_id.val() || shift_id.val().length === 0) {
                        $("#shift_id_error").html("Please select shift before submitting.");
                        $(shift_id).parent().find('.select2-selection').addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#shift_id_error").html("");
                    }

                    if (!psp_id.val() || psp_id.val().length === 0) {
                        $("#psp_id_error").html("Please select a PSP before submitting.");
                        $(psp_id).parent().find('.select2-selection').addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#psp_id_error").html("");
                    }

                    if (!code.val() || code.val().length === 0) {
                        $("#code_error").html("Please enter a unique code before submitting.");
                        $(code).addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#code_error").html("");
                    }

                    if (!gender.val() || gender.val().length === 0) {
                        $("#gender_error").html("Please select gender before submitting.");
                        $(gender).parent().find('.select2-selection').addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#gender_error").html("");
                    }

                    // if (!marital_status.val() || marital_status.val().length === 0) {
                    //     $("#marital_status_error").html("Please select marital status before submitting.");
                    //     form_validated = false;
                    //     e.preventDefault();
                    // } else {
                    //     $("#marital_status_error").html("");
                    // }

                    // if (!mobile_number.val() || mobile_number.val().length === 0) {
                    //     $("#mobile_number_error").html("Please Enter Adhar number before submitting.");
                    //     form_validated = false;
                    // }

                    if (!mobile_number.val() || !/^\d{10}$/.test(mobile_number.val())) {
                        $("#mobile_number_error").html("Please enter a valid 10-digit mobile number.");
                        $(mobile_number).addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#mobile_number_error").html("");
                    }

                    if (!adhar_number.val() || !/^\d{12}$/.test(adhar_number.val())) {
                        $("#adhar_number_error").html("Please enter a valid 12-digit adhar number.");
                        $(adhar_number).addClass('is-invalid');
                        form_validated = false;
                        e.preventDefault();
                    } else {
                        $("#adhar_number_error").html("");
                    }


                    // if ((marital_status.val() == 'Married' && gender.val() == 'female') && (!husband_name.val() || husband_name.val().length === 0)) {
                    //     $("#husband_name_error").html("Please enter Husband Name.");
                    //     form_validated = false;
                    // } else {
                    //     $("#husband_name_error").html("");
                    // }



                    if (form_validated === false) {
                        alert("Please fill all mandatory fields");
                        e.preventDefault();
                    }
                });

            });
        </script>
    </x-slot>

</x-app-layout>