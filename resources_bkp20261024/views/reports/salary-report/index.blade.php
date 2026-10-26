<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <form id="filter_form" class="card mb-3" method="GET" action="{{route('reports.salary.index')}}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="psp" id="psp" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select PSP" data-allow-clear="true">
                                    <option value="all" {{ request('psp')=='all' ? 'selected' : '' }}>All PSP</option>
                                    @foreach ($psps as $psp)
                                    <option value="{{$psp->id}}" <?= @$_REQUEST['psp'] == $psp->id ? 'selected' : '' ?>>
                                        {{$psp->name}}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="psp" class="form-label">PSP</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="workers[]" id="workers" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="All workers" data-allow-clear="true"
                                    multiple>
                                    <option value="all" {{ in_array('all', (array) request('workers')) || empty( (array)
                                        request('workers')) ? 'selected' : '' }}>All Workers</option>
                                    @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ in_array($worker->id, (array)
                                        request('workers')) ? 'selected' : '' }}>
                                        {{ $worker->name }} ({{$worker->code}})
                                    </option>
                                    @endforeach
                                </select>
                                <label for=" workers" class="form-label">workers</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker" id="yearmonth_picker" data-wrap="true">
                                    <input type="text" id="yearmonth" class="form-control form-control-sm"
                                        name="yearmonth"
                                        value="{{ $yearMonth }}"
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


            <table id="salary_report_table" class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap text-center">
                <thead>
                    <tr>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            <strong>
                                <span class="d-block">Worker</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>

                        <th class="text-dark fw-bolder text-hover-primary fs-6 ">
                            <strong>
                                <span class="d-block">Date of joining</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>

                        <th class="text-dark fw-bolder text-hover-primary fs-6 ">
                            <strong>
                                <span class="d-block">Days in {{date('M, y', strtotime($yearMonth.'-01'))}}</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>

                        <th class="text-dark fw-bolder text-hover-primary fs-6 ">
                            <strong>
                                <span class="d-block">Attn in {{date('M, y', strtotime($yearMonth.'-01'))}}</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>


                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Basic Salary</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">HRA</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>

                        {{--
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Conveyance</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Medical Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Spl Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Fuel Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Vacation Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        --}}

                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Other Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-warning">
                            <strong>
                                <span class="d-block">Gross</span>
                                <span class="d-block" style="font-size:0.6rem"> (Rate of pay) </span>
                            </strong>
                        </th>


                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Basic Salary</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">HRA</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>

                        {{--
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Conveyance</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Medical Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Spl Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Fuel Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Vacation Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        --}}

                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Other Allow.</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-success">
                            <strong>
                                <span class="d-block">Gross</span>
                                <span class="d-block" style="font-size:0.6rem"> (Earned) </span>
                            </strong>
                        </th>


                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-danger">
                            <strong>
                                <span class="d-block">Employee PF</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-light-danger">
                            <strong>
                                <span class="d-block">Employee ESIC</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-danger">
                            <strong>
                                <span class="d-block">Total Deduction</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 bg-success">
                            <strong>
                                <span class="d-block">Net Salary</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>

                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            <strong>
                                <span class="d-block">Action</span>
                                <span class="d-block" style="font-size:0.6rem">&nbsp;</span>
                            </strong>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($processedSalaries as $processedSalary)
                    <tr>
                        <td>
                            {{$processedSalary->worker->name}} ({{ $processedSalary->worker->code }})
                        </td>

                        <td>
                            {{
                                $processedSalary->worker?->date_of_joining
                                ? \Carbon\Carbon::parse($processedSalary->worker->date_of_joining)->format('d M, Y')
                                : ''
                            }}
                        </td>
                        <td>
                            {{date('t', strtotime($yearMonth.'-01'))}}
                        </td>
                        <td>
                            {{$processedSalary?->paid_days}}
                        </td>

                        <td>
                            {{$processedSalary?->salary_structure?->basic}}
                        </td>
                        <td>
                            {{$processedSalary?->salary_structure?->hra}}
                        </td>
                        {{--
                        <td>
                            {{$processedSalary?->salary_structure?->conveyance}}
                        </td>
                        <td>
                            {{$processedSalary?->salary_structure?->medical_allowance}}
                        </td>
                        <td>
                            {{$processedSalary?->salary_structure?->special_allowance}}
                        </td>
                        <td>
                            {{$processedSalary?->salary_structure?->fuel_allowance}}
                        </td>
                        <td>
                            {{$processedSalary?->salary_structure?->vacation_allowance}}
                        </td>
                        --}}
                        <td>
                            {{$processedSalary?->salary_structure?->other_allowance}}
                        </td>
                        <td>
                            {{
                                $processedSalary?->salary_structure?->basic
                                + $processedSalary?->salary_structure?->hra
                                + $processedSalary?->salary_structure?->conveyance
                                + $processedSalary?->salary_structure?->medical_allowance
                                + $processedSalary?->salary_structure?->special_allowance
                                + $processedSalary?->salary_structure?->fuel_allowance
                                + $processedSalary?->salary_structure?->vacation_allowance
                                + $processedSalary?->salary_structure?->other_allowance;
                            }}
                        </td>



                        <td>
                            {{$processedSalary->basic}}
                        </td>
                        <td>
                            {{$processedSalary->hra}}
                        </td>
                        {{--
                        <td>
                            {{$processedSalary->conveyance}}
                        </td>
                        <td>
                            {{$processedSalary->medical_allowance}}
                        </td>
                        <td>
                            {{$processedSalary->special_allowance}}
                        </td>
                        <td>
                            {{$processedSalary->fuel_allowance}}
                        </td>
                        <td>
                            {{$processedSalary->vacation_allowance}}
                        </td>
                        --}}
                        <td>
                            {{$processedSalary->other_allowance}}
                        </td>
                        <td>
                            {{$processedSalary->gross}}
                        </td>
                        <td>
                            {{$processedSalary->pf_employee}}
                        </td>
                        <td>
                            {{$processedSalary->esic_employee}}
                        </td>
                        <td>
                            {{$processedSalary->gross - $processedSalary->net_salary}}
                        </td>
                        <td>
                            {{$processedSalary->net_salary}}
                        </td>

                        <td>
                            <small>Coming Soon</small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center" colspan="5">
                            Nothing Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>


        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
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

                $(document).on('change', 'form#filter_form select#psp', function() {
                    getFilteredWorkers();
                });

                function getFilteredWorkers() {
                    let selectedWorkerId = new URLSearchParams(window.location.search).get("workers");
                    $.ajax({
                        url: "{{ route('reports.salary.get-filtered-workers') }}",
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
                                workersDropdown.append('<option value="' + worker.id + '" ' + selected + '>' + worker.name + ' (' + worker.code + ')</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log("Error fetching workers: ", error);
                        }
                    });
                }

                var salary_report_table = $("#salary_report_table").DataTable({
                    "dom": '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    "buttons": [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    // "deferRender": true,
                    "processing": true,
                    // "serverSide": true,
                    // "ajax": "{{ route('workers.index') }}",
                    // "columns": columns,
                    "oLanguage": {
                        "sSearch": ""
                    },
                    // "order": [
                    //     [2, 'asc']
                    // ],
                    "scrollX": true,
                    "scrollY": "60vh",
                    "scrollCollapse": true,
                    "fixedColumns": {
                        left: 1,
                        right: 1
                    },
                    "columnDefs": [{
                            "className": 'py-1 border-end border-secondary td-border-left text-center',
                            "targets": [0]
                        },
                        {
                            "className": 'py-1 border-start border-secondary td-border-left text-center',
                            "targets": [-1]
                        },
                        {
                            "className": 'py-1 text-center',
                            "targets": '_all'
                        },
                    ],
                    "lengthMenu": [
                        [10, 25, 50, 100, -1], // The actual values
                        [10, 25, 50, 100, "All"] // The labels displayed
                    ],
                    "pageLength": 10,
                    "language": {
                        "processing": "<div>Loading data, please wait...</div>",
                    },
                });
            })
        </script>
    </x-slot>

</x-app-layout>
