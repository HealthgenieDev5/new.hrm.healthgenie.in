<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">
            <form id="filter_form" class="card mb-3" method="GET"
                action="{{ route('dashboard.synced-call-records') }}">
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="form-floating">
                                <select name="call_type" id="call_type" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select Call Type" data-allow-clear="true">
                                    <option value="all" {{ request('call_type') == 'all' ? 'selected' : '' }}>All Call
                                        Types</option>
                                    @foreach ($callTypes as $callType)
                                        <option value="{{ $callType }}"
                                            {{ request('call_type') == $callType ? 'selected' : '' }}>
                                            {{ ucfirst($callType) }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="call_type" class="form-label">Call Type</label>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker" id="date_range_picker" data-wrap="true">
                                    <input type="text" id="date_range" class="form-control form-control-sm"
                                        name="date_range" value="{{ $defaultDateRange }}"
                                        placeholder="YYYY-MM-DD to YYYY-MM-DD" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <label class="form-label">Call Date Range</label>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2 col-md-4">
                            <div >
                                <button type="submit" id="filter_form_submit"
                                    class="btn btn-primary btn-sm">Filter</button>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-12">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="card card-flush h-100 border border-primary border-dashed">
                                        <div class="card-body py-4 px-4">
                                            <div class="text-muted fs-8 fw-semibold text-uppercase mb-1">Incoming</div>
                                            <div class="fs-2 fw-bold text-primary">{{ $callTypeStats['incoming'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="card card-flush h-100 border border-success border-dashed">
                                        <div class="card-body py-4 px-4">
                                            <div class="text-muted fs-8 fw-semibold text-uppercase mb-1">Outgoing</div>
                                            <div class="fs-2 fw-bold text-success">{{ $callTypeStats['outgoing'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="card card-flush h-100 border border-danger border-dashed">
                                        <div class="card-body py-4 px-4">
                                            <div class="text-muted fs-8 fw-semibold text-uppercase mb-1">Missed</div>
                                            <div class="fs-2 fw-bold text-danger">{{ $callTypeStats['missed'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <table id="synced_call_records_table"
                class="table table-row-bordered table-row-gray-600 align-middle gy-1 nowrap">
                <thead>
                    <tr>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Phone Number</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Call Type</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Duration</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Call Timestamp</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#date_range_picker').flatpickr({
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    altInputClass: "form-control form-control-sm",
                });

                $('#synced_call_records_table').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body position-relative py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dashboard.synced-call-records.datatable') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: function(d) {
                            d.filter_call_type = $('#call_type').val();
                            d.filter_date_range = $('#date_range').val();
                        }
                    },
                    columns: [{
                            data: 'phone_number',
                            name: 'call_records.phone_number'
                        },
                        {
                            data: 'call_type',
                            name: 'call_records.call_type'
                        },
                        {
                            data: 'duration',
                            name: 'call_records.duration',
                            render: {
                                _: 'display',
                                sort: 'sort'
                            }
                        },
                        {
                            data: 'call_timestamp',
                            name: 'call_records.call_timestamp',
                            render: {
                                _: 'display',
                                sort: 'sort'
                            }
                        }
                    ],
                    order: [
                        [3, 'desc']
                    ],
                    scrollX: true,
                    scrollY: "60vh",
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
                        processing: "<div>Loading data, please wait...</div>",
                    },
                    oLanguage: {
                        sSearch: ""
                    }
                });

                $('#synced_call_records_table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title">{{ $pageTitle }}</h3>'
                );
            });
        </script>
    </x-slot>

</x-app-layout>
