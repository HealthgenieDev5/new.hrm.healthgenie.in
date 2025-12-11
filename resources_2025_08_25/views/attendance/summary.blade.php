<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">

            <form class="card w-100 mb-7" id="filter_form" method="GET" action="{{ route('attendance.summary') }}">

                <div class="card-body pb-5">
                    <div class="row">
                        <div class="col-4 col-lg-2 col-md-3">
                            @if (!$user->hasRole('PSP'))
                                <div class="mb-5">
                                    <x-floating-select-input id="psp" name="psp"
                                        class="form-select form-select-sm" label="PSP" :options="$psps"
                                        :value="request('psp') ?? 'all'" placeholder="Select PSP" include_blank />
                                </div>
                            @endif
                        </div>

                        <div class="col-4 col-lg-2 col-md-3">
                            <div class="mb-5">
                                <x-floating-select-input id="workers" name="workers"
                                    class="form-select form-select-sm" label="workers" :options="$workers"
                                    :value="request('workers') ?? 'all'" placeholder="Select workers" data-allow-clear="true" multiple
                                    select_for="worker" include_blank />
                            </div>
                        </div>

                        <div class="col-4 col-lg-2 col-md-3">
                            @php
                                $shift_types = collect([
                                    (object) ['id' => 'day', 'name' => 'Day'],
                                    (object) ['id' => 'night', 'name' => 'Night'],
                                ]);
                            @endphp
                            <div class="mb-5">
                                <x-floating-select-input id="shift_type" name="shift_type"
                                    class="form-select form-select-sm" label="Shift Type" :options="$shift_types"
                                    :value="request('shift_type') ?? 'all'" placeholder="Select Shift Type" data-allow-clear="true"
                                    include_blank />
                            </div>
                        </div>

                        <div class="col-4 col-lg-2 col-md-3">
                            @php
                                $employment_statuses = collect([
                                    (object) ['id' => 'active', 'name' => 'Active'],
                                    (object) ['id' => 'non-active', 'name' => 'Non Active'],
                                ]);
                            @endphp
                            <div class="mb-5">
                                <x-floating-select-input id="employment_status" name="employment_status"
                                    class="form-select form-select-sm" label="Status" :options="$employment_statuses"
                                    :value="request('employment_status') ?? 'active'" placeholder="Select status" data-allow-clear="true"
                                    include_blank />
                            </div>
                        </div>

                        <div class="col-4 col-lg-2 col-md-3">
                            <div class="mb-5">
                                <div class="form-floating-flatpickr">
                                    <div class="input-group input-group-flatpicker" id="date_range_picker"
                                        data-wrap="true">
                                        <input type="text" id="date_range" class="form-control form-control-sm"
                                            name="date_range" value="{{ $date_range }}"
                                            placeholder="YYYY-MM-DD to YYYY-MM-DD" data-input data-open>
                                        <span class="input-group-text cursor-pointer" data-toggle>
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <label class="required form-label">Date Range</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-4 col-lg-2 col-md-3">
                            <div class="mb-5">
                                <button type="submit" class="btn btn-sm btn-primary btn-flex position-relative">
                                    <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1 text-current">
                                        <i class="fa fa-filter text-white"></i>
                                    </span>
                                    Filter
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge badge-circle badge-danger">
                                        <?= count(array_filter(request()->query())) ?>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!--begin::Accordion-->
                    <div class="accordion" id="show_more_filter_accordion">
                        <div class="accordion-item border-0">
                            <p class="accordion-header text-center " id="show_more_filter_header">
                                <small style="">
                                    <a id="show_more_filter_toggle" href="#" class="btn btn-sm btn-link"
                                        data-bs-toggle="collapse" data-bs-target="#show_more_filter"
                                        aria-expanded="false" aria-controls="kt_accordion_1_body_1">
                                        Show More
                                    </a>
                                </small>
                            </p>
                            <div id="show_more_filter" class="accordion-collapse collapse pt-5"
                                aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#show_more_filter_accordion">
                                <div class="row">
                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="designation" label="Designation"
                                                :options="$designations" :value="request('designation') ?? 'all'" placeholder="Select Designation"
                                                data-allow-clear="true" include_blank />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="workertype" label="Worker Type"
                                                :options="$workerTypes" :value="request('workertype') ?? 'all'" placeholder="Select Worker Type"
                                                data-allow-clear="true" include_blank />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="workingarea" label="Working Area"
                                                :options="$workingareas" :value="request('workingarea') ?? 'all'" placeholder="Select Working Area"
                                                data-allow-clear="true" include_blank />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="production_product"
                                                label="Production Product" :options="$productionProducts" :value="request('production_product') ?? 'all'"
                                                placeholder="Select Production Product" data-allow-clear="true"
                                                include_blank />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="level" label="Level" :options="$levels"
                                                :value="request('level') ?? 'all'" placeholder="Select Level" data-allow-clear="true"
                                                include_blank />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="division" label="Division"
                                                placeholder="Select division" data-allow-clear="true" />
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="department" label="Department"
                                                placeholder="Select department" data-allow-clear="true" />
                                            <small class="text-danger" style="font-size:0.7rem">* Departments will be
                                                loaded after you select a division</small>
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="subdepartment" label="Sub Department"
                                                placeholder="Select Sub Department" data-allow-clear="true" />
                                            <small class="text-danger" style="font-size:0.7rem">* Sub Departments will
                                                be loaded after you select a department</small>
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="line" label="Line"
                                                placeholder="Select Line" data-allow-clear="true" />
                                            <small class="text-danger" style="font-size:0.7rem">* Lines will be loaded
                                                after you select a sub department</small>
                                        </div>
                                    </div>

                                    <div class="col-4 col-lg-2 col-md-3">
                                        <div class="mb-5">
                                            <x-floating-select-input name="process" label="Process"
                                                placeholder="Select Process" data-allow-clear="true" />
                                            <small class="text-danger" style="font-size:0.7rem">* Processes will be
                                                loaded after you select a line</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Accordion-->

                </div>
            </form>

            <table id="workerSummaryTable"
                class="table table-row-bordered table-row-gray-600 align-middle gy-1 nowrap ">
                <thead>
                    <tr>
                        @foreach ($workerSummaryColumns as $col)
                            <th class="text-dark fw-bolder text-hover-primary fs-6">
                                {!! $col['title'] !!}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            </table>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
        <script src="{{ asset('js/autoscroll-datatable.js') }}"></script>
        <script>
            $(document).ready(function() {

                $('#date_range_picker').flatpickr({
                    altInput: true,
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altFormat: "d M Y",
                    altInputClass: "form-control form-control-sm",
                    maxDate: "today"
                });

                $(document).on('change', 'form#filter_form select#psp', function() {
                    getFilteredWorkers();
                });

                function getFilteredWorkers() {
                    let selectedWorkerId = new URLSearchParams(window.location.search).get("workers");
                    $.ajax({
                        url: "{{ route('attendance.get-filtered-workers') }}",
                        type: "GET",
                        data: {
                            psp: $("form#filter_form select#psp").val()
                        },
                        success: function(response) {
                            let workersDropdown = $('form#filter_form select#workers');
                            workersDropdown.empty();
                            workersDropdown.append('<option value="all">All Workers</option>');

                            $.each(response, function(index, worker) {
                                let selected = (worker.id == selectedWorkerId) ? "selected" : "";
                                workersDropdown.append('<option value="' + worker.id + '" ' +
                                    selected + '>' + worker.name + ' (' + worker.code +
                                    ')</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log("Error fetching workers: ", error);
                        }
                    });
                }

                const requestDivision = '<?php echo request('division') ?? ''; ?>';
                const requestDepartment = '<?php echo request('department') ?? ''; ?>';
                const requestSubdepartment = '<?php echo request('subdepartment') ?? ''; ?>';
                const requestLine = '<?php echo request('line') ?? ''; ?>';
                const requestProcess = '<?php echo request('process') ?? ''; ?>';

                const divisionsData = <?php echo json_encode($divisions); ?>;

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


                // Step 1: Set Division
                updateDropdown($("#division"), divisionsData, "Select Division");
                if (requestDivision) {
                    setTimeout(() => {
                        $("#division").val(requestDivision).trigger("change");
                    }, 10);
                }
                $(document).on("change", "#division", function() {
                    let division = $(this).val();
                    $("#department").html('');
                    $("#subdepartment").html('');
                    $("#line").html('');
                    $("#process").html('');
                    fetchDepartments(division, requestDepartment);
                });

                function fetchDepartments(division, requestDepartment = null) {
                    let departments = divisionsData.find(d => d.id == division)?.departments || [];
                    let $dropdown = $("#department");
                    updateDropdown($dropdown, departments, "Select Department");
                    if (requestDepartment) {
                        setTimeout(() => {
                            $dropdown.val(requestDepartment).trigger("change");
                        }, 10);
                    }
                }

                // When Department changes, fetch Subdepartments
                $(document).on("change", "#department", function() {
                    let department = $(this).val();
                    $("#subdepartment").html('');
                    $("#line").html('');
                    $("#process").html('');
                    fetchSubdepartments(department, requestSubdepartment);
                });

                function fetchSubdepartments(department, requestSubdepartment = null) {
                    let subdepartments = divisionsData.flatMap(d => d.departments)
                        .find(dep => dep.id == department)?.subdepartments || [];
                    let $dropdown = $("#subdepartment");
                    updateDropdown($dropdown, subdepartments, "Select Subdepartment");
                    if (requestSubdepartment) {
                        setTimeout(() => {
                            $dropdown.val(requestSubdepartment).trigger("change");
                        }, 10);
                    }
                }

                // When Subdepartment changes, fetch Lines
                $(document).on("change", "#subdepartment", function() {
                    let subdepartment = $(this).val();
                    $("#line").html('');
                    $("#process").html('');
                    fetchLines(subdepartment, requestLine);
                });

                function fetchLines(subdepartment, requestLine = null) {
                    let lines = divisionsData.flatMap(d => d.departments)
                        .flatMap(dep => dep.subdepartments)
                        .find(sub => sub.id == subdepartment)?.lines || [];
                    let $dropdown = $("#line");

                    updateDropdown($dropdown, lines, "Select Line");
                    if (requestLine) {
                        setTimeout(() => {
                            $dropdown.val(requestLine).trigger("change");
                        }, 10);
                    }
                }

                // When Line changes, fetch Processes
                $(document).on("change", "#line", function() {
                    let line = $(this).val();
                    $("#process").html('');
                    fetchProcesses(line, requestProcess);
                });

                function fetchProcesses(line, requestProcess = null) {
                    let processes = divisionsData.flatMap(d => d.departments)
                        .flatMap(dep => dep.subdepartments)
                        .flatMap(sub => sub.lines)
                        .find(ln => ln.id == line)?.processes || [];

                    let $dropdown = $("#process");

                    updateDropdown($dropdown, processes, "Select Process");
                    if (requestProcess) {
                        setTimeout(() => {
                            $dropdown.val(requestProcess).trigger('change');
                        }, 10);
                    }
                }

                const $toggleShowMoreBtn = $('#show_more_filter_toggle');
                const $collapseElShowMore = $('#show_more_filter');
                $collapseElShowMore.on('show.bs.collapse', function() {
                    $toggleShowMoreBtn.text('Show less');
                });
                $collapseElShowMore.on('hide.bs.collapse', function() {
                    $toggleShowMoreBtn.text('Show more');
                });


                var userIsPSP = <?php echo (bool) $user->hasRole('PSP') ? 'true' : 'false'; ?>;
                let workerSummaryColumns = [
                    <?php
                    foreach ($workerSummaryColumns as $col) {
                        echo "{ data: '" . $col['data'] . "', name: '" . $col['name'] . "' },";
                    }
                    ?>
                ];
                var filterDrawerElement = document.querySelector("#filter_form_drawer");
                var filterDrawer = KTDrawer.getInstance(filterDrawerElement);
                $("#workerSummaryTable").DataTable({
                    dom: '<"card mb-5"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body position-relative py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: (
                        userIsPSP ? [] : [{
                            extend: 'excelHtml5',
                            title: '',
                            text: '<i class="fa-solid fa-file-excel"></i> Excel',
                            className: 'btn-sm border border-primary',
                        }]
                    ),
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    ajax: {
                        url: "{{ route('attendance.get-sumamry') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                        },
                        data: function(d) {
                            const formData = $('#filter_form').serializeArray();
                            const workers = [];
                            formData.forEach(item => {
                                if (item.name === 'workers[]') {
                                    workers.push(item.value);
                                } else {
                                    d['filter_' + item.name] = item.value;
                                }
                            });
                            d.filter_workers = workers;
                        }
                    },
                    columns: workerSummaryColumns,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    language: {
                        processing: "<div>Loading data, please wait...</div>",
                    },
                    oLanguage: {
                        sSearch: ""
                    },
                    lengthMenu: [
                        userIsPSP ? [10, 25, 50, 100, -1] : [10, 25, 50, 100, -1],
                        ['10 Workers', '25 Workers', '50 Workers', '100 Workers', "All Workers"]
                    ],
                    pageLength: userIsPSP ? 10 : 50,
                    columnDefs: [{
                            "className": 'text-danger text-center',
                            "targets": [17]
                        },
                        {
                            "className": 'text-center',
                            "targets": '_all'
                        },
                    ],
                    createdRow: function(row, data, dataIndex) {
                        if (data.total_missed_punch > 0) {
                            $(row).css('background-color', '#ffcccc');
                        }
                    },
                    order: [
                        [17, 'desc']
                    ],
                    fixedColumns: {
                        left: 4,
                        right: 0
                    }
                });
                $('#workerSummaryTable_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title"><?= $pageTitle ?></h3>'
                );

            })
        </script>
    </x-slot>

</x-app-layout>
