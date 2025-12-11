<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="create_worker" class="card" enctype="multipart/form-data" method="POST"
                action="{{ route('workers.store')}}">
                @csrf
                <input type="hidden" name="psp_id" value="{{ $user->psp->id }}">
                <input type="hidden" name="employment_status" value="Active">
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">

                        <div class="col-lg-4 mb-3">
                            <label class="required form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control form-control-solid" placeholder="worker Name" />
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="name_error">{{ $errors->first('name') ?? ''
                                }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-4 mb-3">
                            <div id="photo_select" class="image-input image-input-outline image-input-empty"
                                data-kt-image-input="true"
                                style="background-image: url(<?= asset('media/avatars/blank.png') ?>)">
                                <div class="image-input-wrapper w-100px h-100px"></div>
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="Change attachment">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" id="photo" name="photo" accept=".png, .jpg, .jpeg"
                                        data-max-size="2048" />
                                    <input type="hidden" name="photo_remove" />
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
                            <br><label class="required form-label" for="photo_select">Photo</label>
                            <small class="text-danger d-block" id="photo_error">{{ $errors->first('photo') ?? ''
                                }}</small>
                        </div>
                    </div>
                    <div class="row gy-5">
                        <div class="col-lg-4 mb-3">
                            <div id="adhar_front_select" class="image-input image-input-outline image-input-empty"
                                data-kt-image-input="true"
                                style="background-image: url(<?= asset('media/svg/files/blank-image.svg') ?>)">
                                <div class="image-input-wrapper w-100px h-100px"></div>
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="Change attachment">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" id="adhar_front" name="adhar_front" accept=".png, .jpg, .jpeg"
                                        data-max-size="2048" />
                                    <input type="hidden" name="adhar_front_remove" />
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
                            <br><label class="required form-label" for="adhar_front_select">Adhar front</label>
                            <small class="text-danger d-block" id="adhar_front_error">{{ $errors->first('adhar_front')
                                ?? '' }}</small>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div id="adhar_back_select" class="image-input image-input-outline image-input-empty"
                                data-kt-image-input="true"
                                style="background-image: url(<?= asset('media/svg/files/blank-image.svg') ?>)">
                                <div class="image-input-wrapper w-100px h-100px"></div>
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                    data-bs-original-title="Change attachment">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" id="adhar_back" name="adhar_back" accept=".png, .jpg, .jpeg"
                                        data-max-size="2048" />
                                    <input type="hidden" name="adhar_back_remove" />
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
                            <br><label class="required form-label" for="adhar_back_select">Adhar back</label>
                            <small class="text-danger d-block" id="adhar_back_error">{{ $errors->first('adhar_back') ??
                                '' }}</small>
                        </div>
                    </div>
                    <div class="row gy-5">

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Father's name</label>
                            <input type="text" name="father_name" value="{{ old('father_name') }}"
                                class="form-control form-control-solid" placeholder="Father's name" />
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="father_name_error">{{ $errors->first('father_name')
                                ?? '' }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Husband's name</label>
                            <input type="text" name="husband_name" value="{{ old('husband_name') }}"
                                class="form-control form-control-solid" placeholder="Husband's name" />
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="husband_name_error">{{ $errors->first('husband_name')
                                ?? '' }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Religion</label>
                            <select name="religion" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Religion">
                                <option {{ old('religion', '' )=='' ? 'selected' : '' }}></option>
                                <option value="Hindu" {{ old('religion', '' )=='Hindu' ? 'selected' : '' }}>Hindu
                                </option>
                                <option value="Muslim" {{ old('religion', '' )=='Muslim' ? 'selected' : '' }}>Muslim
                                </option>
                                <option value="Christian" {{ old('religion', '' )=='Christian' ? 'selected' : '' }}>
                                    Christian</option>
                                <option value="Sikh" {{ old('religion', '' )=='Sikh' ? 'selected' : '' }}>Sikh</option>
                                <option value="Jain" {{ old('religion', '' )=='Jain' ? 'selected' : '' }}>Jain</option>
                                <option value="Budhist" {{ old('religion', '' )=='Budhist' ? 'selected' : '' }}>Budhist
                                </option>
                            </select>
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="religion_error">{{ $errors->first('religion') ?? ''
                                }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Shift</label>
                            <select name="shift_id" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Shift">
                                <option></option>
                                @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id')==$shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }}
                                </option>
                                @endforeach
                            </select>
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="shift_id_error">{{ $errors->first('shift_id') ?? ''
                                }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Mobile number</label>
                            <input type="text" name="mobile_number" value="{{ old('mobile_number') }}"
                                class="form-control form-control-solid" placeholder="Mobile number" />
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="mobile_number_error">{{
                                $errors->first('mobile_number') ?? '' }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Gender</label>
                            <select name="gender" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Gender">
                                <option></option>
                                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender')=='other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="gender_error">{{ $errors->first('gender') ?? ''
                                }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Marital status</label>
                            <select name="marital_status" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select marital status">
                                <option></option>
                                <option value="Unmarried" {{ old('marital_status')=='Unmarried' ? 'selected' : '' }}>
                                    Unmarried</option>
                                <option value="Married" {{ old('marital_status')=='Married' ? 'selected' : '' }}>Married
                                </option>
                                <option value="Divorced" {{ old('marital_status')=='Divorced' ? 'selected' : '' }}>
                                    Divorced</option>
                                <option value="Widowed" {{ old('marital_status')=='Widowed' ? 'selected' : '' }}>Widowed
                                </option>
                            </select>
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="marital_status_error">{{
                                $errors->first('marital_status') ?? '' }}</small>
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Adhar number</label>
                            <input type="text" name="adhar_number" value="{{ old('adhar_number') }}"
                                class="form-control form-control-solid" placeholder="Adhar number" />
                            <!--begin::Error Message-->
                            <small class="text-danger d-block" id="adhar_number_error">{{ $errors->first('adhar_number')
                                ?? '' }}</small>
                            <!--end::Error Message-->
                        </div>

                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->


    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('.input-group-flatpicker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: "form-control"
                })
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

                $(document).on('submit', '#create_worker', function(e) {
                    var photoInput = $('#photo')[0];
                    var adhar_frontInput = $('#adhar_front')[0];
                    var adhar_backInput = $('#adhar_back')[0];
                    var name = $('input[name="name"]');
                    var father_name = $('input[name="father_name"]');
                    var husband_name = $('input[name="husband_name"]');
                    var religion = $('select[name="religion"]');
                    var shift_id = $('select[name="shift_id"]');
                    var gender = $('select[name="gender"]');
                    var marital_status = $('select[name="marital_status"]');
                    var mobile_number = $('input[name="mobile_number"]');
                    var adhar_number = $('input[name="adhar_number"]');
                    var form_validated = true;
                    if (!photoInput.files || photoInput.files.length === 0) {
                        $("#photo_error").html("Please select a photo before submitting.");
                        form_validated = false;
                    }else{
                        $("#photo_error").html("");
                    }
                    if (!adhar_frontInput.files || adhar_frontInput.files.length === 0) {
                        $("#adhar_front_error").html("Please select a adhar front photo before submitting.");
                        form_validated = false;
                    }else{
                        $("#adhar_front_error").html("");
                    }
                    if (!adhar_backInput.files || adhar_backInput.files.length === 0) {
                        $("#adhar_back_error").html("Please select a adhar back photo before submitting.");
                        form_validated = false;
                    }else{
                        $("#adhar_back_error").html("");
                    }
                    if (!name.val() || name.val().length === 0) {
                        $("#name_error").html("Please Enter name before submitting.");
                        form_validated = false;
                    }else{
                        $("#name_error").html("");
                    }
                    if (!father_name.val() || father_name.val().length === 0) {
                        $("#father_name_error").html("Please Enter Father's name before submitting.");
                        form_validated = false;
                    }else{
                        $("#father_name_error").html("");
                    }
                    if (!religion.val() || religion.val().length === 0) {
                        $("#religion_error").html("Please select religion before submitting.");
                        form_validated = false;
                    }else{
                        $("#religion_error").html("");
                    }
                    if (!shift_id.val() || shift_id.val().length === 0) {
                        $("#shift_id_error").html("Please select shift before submitting.");
                        form_validated = false;
                    }else{
                        $("#shift_id_error").html("");
                    }
                    if (!gender.val() || gender.val().length === 0) {
                        $("#gender_error").html("Please select gender before submitting.");
                        form_validated = false;
                    }else{
                        $("#gender_error").html("");
                    }
                    if (!marital_status.val() || marital_status.val().length === 0) {
                        $("#marital_status_error").html("Please select marital status before submitting.");
                        form_validated = false;
                    }else{
                        $("#marital_status_error").html("");
                    }

                    // if (!mobile_number.val() || mobile_number.val().length === 0) {
                    //     $("#mobile_number_error").html("Please Enter Adhar number before submitting.");
                    //     form_validated = false;
                    // }

                    if (!mobile_number.val() || !/^\d{10}$/.test(mobile_number.val())) {
                        $("#mobile_number_error").html("Please enter a valid 10-digit mobile number.");
                        form_validated = false;
                    } else {
                        $("#mobile_number_error").html("");
                    }

                    if (!adhar_number.val() || !/^\d{12}$/.test(adhar_number.val())) {
                        $("#adhar_number_error").html("Please enter a valid 12-digit adhar number.");
                        form_validated = false;
                    } else {
                        $("#adhar_number_error").html("");
                    }


                    if ( (marital_status.val() == 'Married' && gender.val() == 'female') && (!husband_name.val() || husband_name.val().length === 0) ) {
                        $("#husband_name_error").html("Please enter Husband Name.");
                        form_validated = false;
                    }else{
                        $("#husband_name_error").html("");
                    }



                    if (form_validated === false) {
                        alert("You have missed to fill some mandatory fields");
                        e.preventDefault();
                    }
                });



            });
        </script>
    </x-slot>

</x-app-layout>