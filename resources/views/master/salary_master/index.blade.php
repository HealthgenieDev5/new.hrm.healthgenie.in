<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <form id="filter_form" class="card mb-3" method="GET" action="{{route('master.salary-master.index')}}">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="psp" id="psp" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select PSP" data-allow-clear="true">
                                    <option value="all" {{ request('psp')=='all' ? 'selected' : '' }}>All PSP</option>
                                    @foreach ($psps as $psp)
                                    <option value="{{$psp->id}}" <?= @$_REQUEST['psp'] == $psp->id ? 'selected' : '' ?>>{{$psp->name}}</option>
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
                            @php
                            $employment_statuses = collect([
                            (object) ['id' => 'active', 'name'=>'Active'],
                            (object) ['id' => 'non-active', 'name'=>'Non Active']
                            ]);
                            @endphp
                            <x-floating-select-input id="employment_status" name="employment_status" class="form-select form-select-sm" label="Status" :options="$employment_statuses" :value="request('employment_status') ?? 'active'" placeholder="Select status" />
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

            <table class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap text-center" id="salary_master_table">
                <thead>
                    <tr>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Name
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Min Wg
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            CTC
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Gross
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            In Hand
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Basic
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Hra
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Other alw
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Bonus allowed
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Bonus
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Pf allowed
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Employee PF
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Employer PF
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Esic allowed
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Employee ESIC
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            Employer ESIC
                        </th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6 text-center">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- @if ($salaryMasters) -->
                    @foreach ($salaryMasters as $salaryMaster)
                    <tr>
                        <td>
                            {{$salaryMaster->name}} ({{$salaryMaster->code}})
                            <br>
                            <small>{{$salaryMaster->employment_status}}</small>
                        </td>
                        <td>
                            {{$salaryMaster?->workerType?->minimum_wage ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->ctc ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->gross ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->inhand ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->basic ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->hra ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->other_allowance ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->bonus_allowed ? 'Yes' : 'N/A'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->bonus ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->pf_allowed ? 'Yes' : 'N/A'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->employeepf ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->employerpf ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->esic_allowed ? 'Yes' : 'N/A'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->employee_esic ?? '0'}}
                        </td>
                        <td>
                            {{$salaryMaster?->salaryStructure?->employer_esic ?? '0'}}
                        </td>
                        <td>
                            <div class="d-flex justify-content-center">
                                @if ($salaryMaster?->salaryStructure?->id > 0 )
                                @hasPermissionTo('Edit Salary Master')
                                <a href="{{ route('master.salary-master.edit', $salaryMaster?->salaryStructure?->id)}}"
                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-salary-master"
                                    data-id="1">
                                    <span class="svg-icon svg-icon-3">
                                        <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                    </span>
                                </a>
                                @endhasPermissionTo
                                @hasPermissionTo('Delete Salary Master')
                                <form action="{{ route('master.salary-master.destroy', $salaryMaster?->salaryStructure?->id) }}"
                                    method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                        onclick="return confirm('Are you sure?')">
                                        <span class="svg-icon svg-icon-3">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </span>
                                    </button>
                                </form>
                                @endhasPermissionTo
                                @else
                                <a href="{{ route('master.salary-master.create', ['worker'=>$salaryMaster->id])}}"
                                    class="btn btn-light-primary btn-sm me-1 create-salary-master"
                                    data-id="1">
                                    <span class="svg-icon svg-icon-3 me-3">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </span>
                                    Create
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <!-- @endif -->
                </tbody>
            </table>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {

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
                                workersDropdown.append('<option value="' + worker.id + '" ' + selected + '>' + worker.name + ' (' + worker.code + ')</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log("Error fetching workers: ", error);
                        }
                    });
                }

                var salary_master_table = $("#salary_master_table").DataTable({
                    dom: '<"card mb-5"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body position-relative py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: [{
                        extend: 'excelHtml5',
                        title: '',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                    }, ],
                    processing: true,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    language: {
                        processing: "<div>Loading data, please wait...</div>",
                    },
                    oLanguage: {
                        sSearch: ""
                    },
                    columnDefs: [{
                        "className": 'border-end border-secondary',
                        "targets": [0]
                    }, {
                        "className": 'border-start border-secondary',
                        "targets": [-1]
                    }, {
                        "className": 'text-center',
                        "targets": '_all'
                    }, ],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        ['10 Workers', '25 Workers', '50 Workers', '100 Workers', "All Workers"]
                    ],
                    pageLength: 50,
                    fixedColumns: {
                        left: 1,
                        right: 1,
                    },
                    order: [
                        [2, 'asc']
                    ]
                });

                $('#salary_master_table_wrapper > .card > .card-header > .card-title').replaceWith('<h3 class="card-title">Salary Master</h3>');
                $('#salary_master_table_wrapper > .card > .card-header > .card-toolbar > .datatable-buttons-container > .dt-buttons').append('<a class="btn btn-light-primary btn-sm border border-primary" href="{{route("master.salary-master.create")}}"><i class="fa fa-plus"></i> Add New</a>');
            });
        </script>
    </x-slot>

</x-app-layout>