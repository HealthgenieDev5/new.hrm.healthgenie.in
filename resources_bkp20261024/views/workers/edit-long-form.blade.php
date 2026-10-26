<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="edit_worker" class="card" enctype="multipart/form-data" method="POST" action="{{ route('workers.update-long-form', $worker->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-header">
                    <h3 class="card-title"><span>{{$worker->name}} ({{$worker->code}}) </span> - <small> PSP: {{$worker->psp->name}}</small></h3>
                </div>
                <div class="card-body p-0 p-md-7">
                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-date-input name="last_working__absconded_date" label="Last Working Date / Absconded Date" :value="$worker?->last_working__absconded_date" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="notice_period_loa" label="Notice Period LOA" :value="$worker?->notice_period_loa" icon="Days" data-numeric="true" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="notice_period_served" label="Notice Period Served" :value="$worker?->notice_period_served" icon="Days" data-numeric="true" data-decimal="true" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="husband_name" label="Spouse Name" :value="$worker?->husband_name" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-date-input name="date_of_birth" label="Date of Birth" :value="$worker?->date_of_birth" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="religion" label="Religion" :options="$religions" :value="$worker->religion" placeholder="Select Religion" data-allow-clear="true" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="marital_status" label="Marital Status" :options="$maritalStatuses" :value="$worker->marital_status" placeholder="Select Marital Status" data-allow-clear="true" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="permanent_city" label="Permanent City" :value="$worker->permanent_city" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="permanent_district" label="Permanent District" :value="$worker->permanent_district" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="permanent_state" label="Permanent State" :value="$worker->permanent_state" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="permanent_pincode" label="Permanent Pincode" :value="$worker->permanent_pincode" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-textarea-input name="permanent_address" label="Permanent Address" :value="$worker->permanent_address" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="present_city" label="Present City" :value="$worker->present_city" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="present_district" label="Present District" :value="$worker->present_district" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="present_state" label="Present State" :value="$worker->present_state" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="present_pincode" label="Present Pincode" :value="$worker->present_pincode" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-textarea-input name="present_address" label="Present Address" :value="$worker->present_address" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="designation_id" label="Designation" :options="$designations" :value="$worker->designation_id" placeholder="Select Designation" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="workertype_id" label="Worker Type" :options="$workerTypes" :value="$worker->workertype_id" placeholder="Select Worker Type" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="working_area_id" label="Working Area" :options="$workingareas" :value="$worker->working_area_id" placeholder="Select Working Area" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="production_product_id" label="Production Product" :options="$productionProducts" :value="$worker->production_product_id" placeholder="Select Production Product" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="level_id" label="Level" :options="$levels" :value="$worker->level_id" placeholder="Select Level" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="uan_number" label="UAN Number" :value="$worker?->uan_number" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3 mb-3">
                            <x-floating-image-input name="uan_photo" label="UAN Photo" :value="$worker->uan_photo" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-text-input name="esic_number" label="ESIC Number" :value="$worker?->esic_number" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="division_id" label="Division" placeholder="Select Division" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="department_id" label="Department" placeholder="Select Department" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="subdepartment_id" label="Sub Department" placeholder="Select Sub Department" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="line_id" label="Line" placeholder="Select Line" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3">
                            <x-floating-select-input name="process_id" label="Process" placeholder="Select Process" />
                        </div>

                    </div>

                    <hr>

                    <div class="row gy-5">

                        <div class="col-lg-4 col-md-6 pt-3 mb-3">
                            <x-floating-image-input name="form_11_photo" label="Form 11 Photo" :value="$worker->form_11_photo" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3 mb-3">
                            <x-floating-image-input name="affidavit_photo" label="Affidavit photo" :value="$worker->affidavit_photo" />
                        </div>

                        <div class="col-lg-4 col-md-6 pt-3 mb-3">
                            <x-floating-image-input name="lop_photo" label="LOP photo" :value="$worker->lop_photo" />
                        </div>

                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-danger btn-sm ">Update Long Form</button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->


    <x-slot name="scripts">


        <script src="{{ asset('plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>


        <script>
            var assignedDivisionId = <?php echo old('division_id', $worker?->division_id) ?? 'null'; ?>;
            var assignedDepartmentId = <?php echo old('department_id', $worker?->department_id) ?? 'null'; ?>;
            var assignedSubdepartmentId = <?php echo old('subdepartment_id', $worker?->subdepartment_id) ?? 'null'; ?>;
            var assignedLineId = <?php echo old('line_id', $worker?->line_id) ?? 'null'; ?>;
            var assignedProcessId = <?php echo old('process_id', $worker?->process_id) ?? 'null'; ?>;

            var divisionsData = <?php echo json_encode($divisions); ?>;

            $(document).ready(function() {

                $('#date_of_birth_picker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    altInputClass: "form-control",
                    maxDate: "<?php echo date('Y-m-d'); ?>"
                });

                $('#last_working__absconded_date').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y',
                    altInputClass: "form-control"
                });

                let divisions = divisionsData;
                let $dropdown = $("#division_id");
                updateDropdown($dropdown, divisions, "Select divisions");
                if (assignedDivisionId) {
                    setTimeout(() => {
                        console.log(assignedDivisionId);
                        $("#division_id").val(assignedDivisionId).trigger("change");

                    }, 10);
                }

                // When Division changes, fetch Departments
                $(document).on("change", "#division_id", function() {
                    let divisionId = $(this).val();
                    fetchDepartments(divisionId, assignedDepartmentId);
                });

                function fetchDepartments(divisionId, selectedDepartmentId = null) {
                    let departments = divisionsData.find(d => d.id == divisionId)?.departments || [];
                    let $dropdown = $("#department_id");

                    updateDropdown($dropdown, departments, "Select Department");

                    if (selectedDepartmentId) {
                        setTimeout(() => {
                            $dropdown.val(selectedDepartmentId).trigger("change");
                        }, 10);
                    }
                }

                // When Department changes, fetch Subdepartments
                $(document).on("change", "#department_id", function() {
                    let departmentId = $(this).val();
                    fetchSubdepartments(departmentId, assignedSubdepartmentId);
                });

                function fetchSubdepartments(departmentId, selectedSubdepartmentId = null) {
                    let subdepartments = divisionsData.flatMap(d => d.departments)
                        .find(dep => dep.id == departmentId)?.subdepartments || [];
                    let $dropdown = $("#subdepartment_id");

                    updateDropdown($dropdown, subdepartments, "Select Subdepartment");

                    if (selectedSubdepartmentId) {
                        setTimeout(() => {
                            $dropdown.val(selectedSubdepartmentId).trigger("change");
                        }, 10);
                    }
                }

                // When Subdepartment changes, fetch Lines
                $(document).on("change", "#subdepartment_id", function() {
                    let subdepartmentId = $(this).val();
                    fetchLines(subdepartmentId, assignedLineId);
                });

                function fetchLines(subdepartmentId, selectedLineId = null) {
                    let lines = divisionsData.flatMap(d => d.departments)
                        .flatMap(dep => dep.subdepartments)
                        .find(sub => sub.id == subdepartmentId)?.lines || [];
                    let $dropdown = $("#line_id");

                    updateDropdown($dropdown, lines, "Select Line");

                    if (selectedLineId) {
                        setTimeout(() => {
                            $dropdown.val(selectedLineId).trigger("change");
                        }, 10);
                    }
                }

                // When Line changes, fetch Processes
                $(document).on("change", "#line_id", function() {
                    let lineId = $(this).val();
                    fetchProcesses(lineId, assignedProcessId);
                    console.log("assignedProcessId", assignedProcessId);
                });

                function fetchProcesses(lineId, selectedProcessId = null) {
                    let processes = divisionsData.flatMap(d => d.departments)
                        .flatMap(dep => dep.subdepartments)
                        .flatMap(sub => sub.lines)
                        .find(line => line.id == lineId)?.processes || [];

                    // console.log("processes", processes);

                    let $dropdown = $("#process_id");

                    updateDropdown($dropdown, processes, "Select Process");

                    if (selectedProcessId) {
                        setTimeout(() => {
                            $dropdown.val(selectedProcessId).trigger('change');
                        }, 10);
                    }
                }

                // Utility function to update dropdowns
                function updateDropdown($dropdown, items, placeholder) {
                    $dropdown.html(`<option value="">${placeholder}</option>`);
                    $.each(items, function(index, item) {
                        $dropdown.append(`<option value="${item.id}">${item.name}</option>`);
                    });

                    if (items.length) {
                        $dropdown.prop("disabled", false);
                        $dropdown.parent().find('.select2-selection--single').addClass('text-dark');
                    } else {
                        $dropdown.prop("disabled", true);
                        $dropdown.parent().find('.select2-selection--single').removeClass('text-dark');
                    }
                }




            });

            $(document).ready(function() {
                $('.image-input').each(function() {
                    var id = $(this).attr('id');
                    var imageInputElement = document.querySelector("#" + id);
                    var imageInput = KTImageInput.getInstance(imageInputElement);

                    var iframe_src_backup = '';

                    imageInput.on("kt.imageinput.changed", function() {
                        var fileInput = $("#" + id).find("input[type=file]")[0];
                        var imageInputWrapper = $("#" + id + " .image-input-wrapper");
                        var lightboxIframe = $("#" + id).find("iframe");
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var extension = fileInput.files[0].name.split('.').pop().toLowerCase();
                            switch (extension) {
                                case 'pdf':
                                    lightboxIframe.attr('src', e.target.result);
                                    imageInputWrapper.find('.preview-button').removeClass('d-none').addClass('d-block');
                                    imageInputWrapper.css({
                                        'background-image': 'url({{asset("media/svg/files/pdf.svg")}})'
                                    });
                                    break;
                                default:
                                    lightboxIframe.attr('src', e.target.result);
                                    imageInputWrapper.find('.preview-button').removeClass('d-none').addClass('d-block');
                                    break;
                            }
                        }
                        reader.readAsDataURL(fileInput.files[0]);
                    });

                    imageInput.on("kt.imageinput.change", function() {
                        var lightboxIframe = $("#" + id).find("iframe");
                        if (iframe_src_backup == '') {
                            iframe_src_backup = lightboxIframe.attr('src');
                        }
                    });

                    imageInput.on("kt.imageinput.canceled", function() {
                        var lightboxIframe = $("#" + id).find("iframe");
                        if (iframe_src_backup !== '') {
                            lightboxIframe.attr('src', iframe_src_backup);
                        } else {
                            var imageInputWrapper = $("#" + id + " .image-input-wrapper");
                            imageInputWrapper.find('.preview-button').removeClass('d-block').addClass('d-none');
                            lightboxIframe.attr('src', '');
                        }
                    });

                    imageInput.on("kt.imageinput.removed", function() {
                        var imageInputWrapper = $("#" + id + " .image-input-wrapper");
                        imageInputWrapper.find('.preview-button').removeClass('d-block').addClass('d-none');
                        var lightboxIframe = $("#" + id).find("iframe");
                        lightboxIframe.attr('src', '');
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('input[data-numeric="true"]').forEach(function(input) {
                    input.addEventListener('keypress', function(e) {
                        const allowDecimal = input.getAttribute('data-decimal') === 'true';
                        const char = String.fromCharCode(e.which);
                        if (!allowDecimal) {
                            if (!/^\d$/.test(char)) {
                                e.preventDefault();
                            }
                        } else {
                            if (!/^\d$/.test(char)) {
                                if (char === '.' && input.value.indexOf('.') === -1) {} else {
                                    e.preventDefault();
                                }
                            }
                        }
                    });
                });
            });
        </script>
        <style>
            .select2-container--bootstrap5 .select2-selection--single.text-dark .select2-selection__placeholder,
            .select2-container--bootstrap5 .select2-selection--single.text-dark .select2-selection__rendered {
                color: #181c32 !important;
            }
        </style>

    </x-slot>

</x-app-layout>