<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            @if (!$user->hasRole('PSP'))
            <form id="filter_form" class="card mb-3" method="GET" action="{{route('workers.index')}}">
                <div class="card-body">
                    <div class="row gy-5">


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
                                <select name="employment_status" id="employment_status" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select Status" data-allow-clear="true">
                                    <option value="all" {{ request('employment_status')=='all' ? 'selected' : '' }}>All Status</option>
                                    <option value="Active" <?= @$_REQUEST['employment_status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Left" <?= @$_REQUEST['employment_status'] == 'Left' ? 'selected' : '' ?>>Left</option>
                                    <option value="Terminated" <?= @$_REQUEST['employment_status'] == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                    <option value="Absconded" <?= @$_REQUEST['employment_status'] == 'Absconded' ? 'selected' : '' ?>>Absconded</option>
                                    <option value="Retired" <?= @$_REQUEST['employment_status'] == 'Retired' ? 'selected' : '' ?>>Retired</option>
                                    <option value="Under Resignation" <?= @$_REQUEST['employment_status'] == 'Under Resignation' ? 'selected' : '' ?>>Under Resignation</option>
                                </select>
                                <label for="employment_status" class="form-label">Employment status</label>
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
            @endif

            <table id="workers_table" class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap ">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            {{ $column['title'] }}
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
        <script>
            $(document).ready(function() {
                let columns = <?= json_encode($columns) ?>;
                var workers_table = $("#workers_table").DataTable({
                    "dom": '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B>f<"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    "buttons": [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary border-end-0',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    "deferRender": true,
                    "processing": true,
                    "serverSide": true,
                    // "ajax": "{{ route('workers.index') }}",
                    "ajax": {
                        url: "{{ route('workers.getworkers') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token
                        },
                        data: function(d) {
                            d.psp = $('#psp').val();
                            d.employment_status = $('#employment_status').val();
                        }
                    },
                    "columns": columns,
                    "oLanguage": {
                        "sSearch": ""
                    },
                    "order": [
                        [2, 'asc']
                    ],
                    "scrollX": true,
                    "scrollY": "60vh",
                    "scrollCollapse": true,
                    "fixedColumns": {
                        left: 3,
                        right: 1
                    },
                    "columnDefs": [{
                            "className": 'py-1 text-center',
                            "targets": [0]
                        },
                        {
                            "className": 'py-1 border-end border-secondary td-border-left text-center',
                            "targets": [2]
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

                $('#workers_table_wrapper > .card > .card-header > .card-title').replaceWith('<h3 class="card-title">Workers</h3>');
                $('#workers_table_wrapper > .card > .card-header > .card-toolbar > .datatable-buttons-container > .dt-buttons').append('<a class="btn btn-light-primary btn-sm border border-primary" href="{{route("workers.create")}}"><i class="fa fa-plus"></i> Add New</a>');
            })
        </script>
    </x-slot>

</x-app-layout>