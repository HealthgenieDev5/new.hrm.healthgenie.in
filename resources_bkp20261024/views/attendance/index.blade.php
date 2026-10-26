<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">

            <style>
                /* .form-floating .select2-selection {
                    height: calc(2.5rem + 2px) !important;
                } */
            </style>
            <form id="filter_form" class="card mb-3" method="GET" action="{{ route('attendance.index') }}">
                <div class="card-body">
                    <div class="row">

                        @if (!$user->hasRole('PSP'))
                            <div class="col-lg-2 col-md-3">
                                <div class="form-floating">
                                    <select name="psp" id="psp" class="form-select form-select-sm"
                                        data-control="select2" data-placeholder="Select PSP" data-allow-clear="true">
                                        <option value="all" {{ request('psp') == 'all' ? 'selected' : '' }}>All PSP
                                        </option>
                                        @foreach ($psps as $psp)
                                            <option value="{{ $psp->id }}"
                                                <?= @$_REQUEST['psp'] == $psp->id ? 'selected' : '' ?>>
                                                {{ $psp->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="psp" class="form-label">PSP</label>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="workers[]" id="workers" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="All workers" data-allow-clear="true"
                                    multiple>
                                    <option value="all"
                                        {{ in_array('all', (array) request('workers')) || empty((array) request('workers')) ? 'selected' : '' }}>
                                        All Workers</option>
                                    @foreach ($workers as $worker)
                                        <option value="{{ $worker->id }}"
                                            {{ in_array($worker->id, (array) request('workers')) ? 'selected' : '' }}>
                                            {{ $worker->name }} ({{ $worker->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <label for=" workers" class="form-label">workers</label>
                            </div>
                        </div>


                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="shift" id="shift" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select Shift" data-allow-clear="true">
                                    <option value="all" {{ request('shift') == 'all' ? 'selected' : '' }}>All Shifts
                                    </option>
                                    @foreach ($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                            {{ request('shift') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="shift" class="form-label">Shift</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker" id="yearmonth_picker" data-wrap="true">
                                    <input type="text" id="yearmonth" class="form-control form-control-sm"
                                        name="yearmonth"
                                        value="{{ request('yearmonth') ?? \Carbon\Carbon::parse($currentDate)->format('Y-m') }}"
                                        placeholder="joining Date" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <label class="required form-label">Month</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <button type="submit" id="filter_form_submit"
                                    class="btn btn-primary btn-sm">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <table id="attendance_table" class="table table-row-bordered table-row-gray-600 align-middle gy-1 nowrap ">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th class="text-dark fw-bolder text-hover-primary fs-6">
                                {!! $column['name'] !!}
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
                $('#yearmonth_picker').flatpickr({
                    altInput: true,
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "Y-m",
                            altFormat: "M Y",
                            altInputClass: "form-control form-control-sm",
                            theme: "dark",
                        })
                    ]
                });

                // setTimeout(() => {
                // getFilteredWorkers();
                // }, 300);

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
            });


            $(document).ready(function() {
                let columns = <?php echo json_encode($columns); ?>;
                var userIsPSP = <?php echo (bool) $user->hasRole('PSP') ? 'true' : 'false'; ?>;

                var attendance_table = $("#attendance_table").DataTable({
                    dom: '<"card "<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body position-relative py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: (userIsPSP ? [] : [{
                        extend: 'excelHtml5',
                        title: '',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            customize: function(xlsx) {
                                const sheet = xlsx.xl.worksheets['sheet1.xml'];
                                $('row:first', sheet).remove();
                                sheet.querySelectorAll('row c[r^="C"]').forEach((el) => {
                                    el.setAttribute('s', '2');
                                });
                            },
                            // columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    if (typeof data === 'string') {
                                        return data
                                            .replace(/<br\s*\/?>/g, '\n')
                                            .replace(/<\/?[^>]+(>|$)/g, '')
                                            .trim();
                                    }
                                    return data;
                                },
                                header: function(data, columnIndex) {
                                    if (typeof data === 'string') {
                                        return data
                                            .replace(/<br\s*\/?>/g, '\n')
                                            .replace(/<\/?[^>]+(>|$)/g, '')
                                            .trim();
                                    }
                                    return data;
                                }
                            }
                        },
                    }, ]),
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('attendance.get-attendance') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                        },
                        data: function(d) {
                            d.psp = $('#filter_form #psp').val();
                            d.workers = $('#filter_form #workers').val();
                            d.shift = $('#filter_form #shift').val();
                            d.yearmonth = $('#filter_form #yearmonth').val();
                        }
                    },
                    columns: columns,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    fixedColumns: {
                        left: userIsPSP ? 2 : 3,
                        right: 0
                    },
                    columnDefs: [{
                            "className": 'text-center',
                            "targets": [0]
                        },
                        {
                            "className": 'border-end border-secondary td-border-left text-center',
                            "targets": userIsPSP ? [1, '-15'] : [2]
                        },
                        {
                            "className": 'text-center',
                            "targets": '_all',
                            "createdCell": function(td, cellData, rowData, row, col) {
                                // Check if it's every 9th row
                                if (!userIsPSP && (row + 1) % 9 === 0) {
                                    $(td).addClass('td-border-separator'); // Add your custom class
                                }
                            }
                        },
                    ],
                    lengthMenu: [
                        userIsPSP ? [10, 25, 50, 100, -1] : [90, 225, 450, 900, -1],
                        ['10 Workers', '25 Workers', '50 Workers', '100 Workers', "All Workers"]
                    ],
                    pageLength: userIsPSP ? 10 : 450,
                    // pageLength: -1,
                    // paging: false,
                    language: {
                        processing: "<div>Loading data, please wait.......</div>",
                        info: "Showing _START_ to _END_ of _TOTAL_ workers",
                        infoFiltered: "(filtered from _MAX_ total workers)"
                    },
                    // Adjust pagination text to reflect workers
                    infoCallback: function(settings, start, end, max, total, pre) {
                        if (userIsPSP) {
                            return `Showing ${start} to ${end} of ${total} rows`;
                        } else {
                            const rowsPerWorker = 9;
                            const totalWorkers = Math.ceil(total / rowsPerWorker);
                            const workersPerPage = settings._iDisplayLength === -1 ? totalWorkers : settings
                                ._iDisplayLength / rowsPerWorker;
                            const workerStart = Math.floor(start / rowsPerWorker) + 1;
                            const workerEnd = settings._iDisplayLength === -1 ? totalWorkers : Math.min(
                                workerStart + workersPerPage - 1, totalWorkers);
                            return `Showing ${workerStart} to ${workerEnd} of ${totalWorkers} workers`;
                        }
                    },
                    oLanguage: {
                        sSearch: ""
                    },
                    ordering: false,
                    initComplete: function() {
                        setTimeout(autoScrollToToday(
                            this.api(),
                            "<?= date('Y-m') ?>",
                            "<?= request()->get('yearmonth', date('Y-m')) ?>"
                        ), 500);
                    }
                });

                $('#attendance_table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title"><?= $pageTitle ?></h3>'
                );



            })
        </script>
    </x-slot>

</x-app-layout>
