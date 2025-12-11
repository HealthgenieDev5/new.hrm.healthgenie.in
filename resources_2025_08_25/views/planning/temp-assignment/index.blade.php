<x-app-layout>
    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <form id="filter_form" class="card mb-3" method="GET"
                action="{{ route('production.temporary-line-assignment.index') }}">
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="psp" id="psp" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select PSP" data-allow-clear="true">
                                    <option value="all" {{ request('psp') == 'all' ? 'selected' : '' }}>All PSP
                                    </option>
                                    @foreach ($psps as $psp)
                                        <option value="{{ $psp->id }}"
                                            {{ request('psp') == $psp->id ? 'selected' : '' }}>{{ $psp->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="psp" class="form-label">PSP</label>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="line" id="line" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select Line" data-allow-clear="true">
                                    <option value="all" {{ request('line') == 'all' ? 'selected' : '' }}>All Lines
                                    </option>
                                    @foreach ($lines as $line)
                                        <option value="{{ $line->id }}"
                                            {{ request('line') == $line->id ? 'selected' : '' }}>{{ $line->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="line" class="form-label">Line</label>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="process" id="process" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select Process" data-allow-clear="true">
                                    <option value="all" {{ request('process') == 'all' ? 'selected' : '' }}>All Processes
                                    </option>
                                    @foreach ($processes as $process)
                                        <option value="{{ $process->id }}"
                                            {{ request('process') == $process->id ? 'selected' : '' }}>{{ $process->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="process" class="form-label">Process</label>
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
                                            {{ request('shift') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="shift" class="form-label">Shift</label>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <input type="text" name="from" id="from" class="form-control form-control-sm"
                                    value="{{ request('from') }}" placeholder="From date" />
                                <label for="from" class="form-label">From</label>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <input type="text" name="to" id="to" class="form-control form-control-sm"
                                    value="{{ request('to') }}" placeholder="To date" />
                                <label for="to" class="form-label">To</label>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <table id="temp_assignment_table"
                class="table table-row-bordered table-row-gray-600 align-middle gy-1 nowrap">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th class="text-dark fw-bolder text-hover-primary fs-6">{{ $column['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#from').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y'
                });
                $('#to').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M Y'
                });

                let columns = @json($columns);
                let table = $('#temp_assignment_table').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body position-relative py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: [{
                        extend: 'excelHtml5',
                        title: '',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary'
                    }],
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('production.temporary-line-assignment.get-datatable') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function(d) {
                            d.filter_psp = $('#psp').val();
                            d.filter_line = $('#line').val();
                            d.filter_process = $('#process').val();
                            d.filter_shift = $('#shift').val();
                            d.filter_from = $('#from').val();
                            d.filter_to = $('#to').val();
                        }
                    },
                    columns: columns,
                    order: [
                        [0, 'asc']
                    ],
                    scrollX: true,
                    scrollY: '60vh',
                    scrollCollapse: true,
                    columnDefs: [{
                        className: 'text-center',
                        targets: '_all'
                    }],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, 'All']
                    ],
                    pageLength: 25,
                    language: {
                        processing: "<div>Loading data, please wait...</div>"
                    },
                    oLanguage: {
                        sSearch: ""
                    }
                });

                $('#temp_assignment_table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title">{{ $pageTitle }}</h3>');
            });
        </script>
    </x-slot>
</x-app-layout>
