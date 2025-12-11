<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <div class="post d-flex flex-column-fluid" id="kt_post">

        <div id="kt_content_container" class="container-fluid">

            <div class="row">
                <div class="col-md-4">
                    <table id="retention_sumamry_table"
                        class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap">
                        <thead>
                            <tr>
                                <th><strong>PSP</strong></th>
                                <th><strong>percentage</strong></th>
                                <th><strong>marks</strong></th>
                                <th><strong>out off</strong></th>
                                <th><strong>Total</strong></th>
                                <th><strong>Left</strong></th>
                                <th><strong>Left %</strong></th>
                                <th><strong>Active </strong></th>
                                <th><strong>Active %</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // dd($pspStats);
                            @endphp
                            @foreach ($pspStats as $pspData)
                                <tr>
                                    @php
                                        $outOff = (count($pspData['activeStats']) + count($pspData['leftStats'])) * 100;
                                        $weightedActive = array_sum(array_column($pspData['activeStats'], 'weighted'));
                                        $weightedLeft = array_sum(array_column($pspData['leftStats'], 'weighted'));
                                        $weightedTotal = $weightedActive + $weightedLeft;

                                        $totalActive = array_sum(array_column($pspData['activeStats'], 'count'));
                                        $totalLeft = array_sum(array_column($pspData['leftStats'], 'count'));
                                    @endphp
                                    <td>{{ $pspData['psp'] }}</td>
                                    <td>{{ round(($weightedTotal / $outOff) * 100, 2) }}%</td>
                                    <td>{{ $weightedActive + $weightedLeft }}</td>
                                    <td>{{ $outOff }}</td>
                                    <td>{{ $totalActive + $totalLeft }}</td>
                                    <td>{{ $totalLeft }}</td>
                                    <td>{{ round(($totalLeft / ($totalActive + $totalLeft)) * 100, 2) }}%</td>
                                    <td>{{ $totalActive }}</td>
                                    <td>{{ round(($totalActive / ($totalActive + $totalLeft)) * 100, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-8">

                    <table id="retention_table"
                        class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap">
                        <thead>
                            <tr>
                                <th><strong>PSP / Details</strong></th>
                                <th><strong>Active Group</strong></th>
                                <th><strong>Active Count</strong></th>
                                <th><strong>Active %</strong></th>
                                <th><strong>Active Weighted Marks</strong></th>
                                <th><strong>Left Group</strong></th>
                                <th><strong>Left Count</strong></th>
                                <th><strong>Left %</strong></th>
                                <th><strong>Left Weighted Marks</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pspStats as $pspData)
                                @php
                                    $activeStatsCount = count($pspData['activeStats']);
                                    $leftStatsCount = count($pspData['leftStats']);
                                    $fullMarks = ($activeStatsCount + $leftStatsCount) * 100;

                                    $weightedActive = array_sum(array_column($pspData['activeStats'], 'weighted'));
                                    $weightedLeft = array_sum(array_column($pspData['leftStats'], 'weighted'));
                                    $weightedTotal = $weightedActive + $weightedLeft;

                                    $totalActive = array_sum(array_column($pspData['activeStats'], 'count'));
                                    $totalLeft = array_sum(array_column($pspData['leftStats'], 'count'));

                                    $num_details_to_show = 6; // PSP name + 5 summary details
                                    $max_group_rows = max($activeStatsCount, $leftStatsCount);
                                    $total_rows_for_psp = max($max_group_rows, $num_details_to_show);

                                    $percentageDisplay = 'N/A';
                                    if ($fullMarks > 0) {
                                        $percentageDisplay = round(($weightedTotal / $fullMarks) * 100, 2) . '%';
                                    } elseif ($weightedTotal == 0) {
                                        $percentageDisplay = '0%';
                                    }
                                @endphp

                                @for ($k = 0; $k < $total_rows_for_psp; $k++)
                                    <tr>
                                        {{-- Column 1: PSP / Details --}}
                                        <td>
                                            @if ($k == 0)
                                                {{ $pspData['psp'] }}
                                            @elseif ($k == 1)
                                                Total Active: {{ $totalActive }}
                                            @elseif ($k == 2)
                                                Total Left: {{ $totalLeft }}
                                            @elseif ($k == 3)
                                                Total Weight: {{ $fullMarks }}
                                            @elseif ($k == 4)
                                                Obtained: {{ $weightedTotal }}
                                            @elseif ($k == 5)
                                                Percentage: {{ $percentageDisplay }}
                                            @else
                                                {{-- Empty for additional group rows beyond detail rows --}}
                                            @endif
                                        </td>

                                        {{-- Active Stats Columns --}}
                                        @php $activeStat = $pspData['activeStats'][$k] ?? null; @endphp
                                        <td>{{ $activeStat['group'] ?? '' }}</td>
                                        <td>{{ $activeStat['count'] ?? '' }}</td>
                                        <td>{{ isset($activeStat['percentage']) ? $activeStat['percentage'] . '%' : '' }}
                                        </td>
                                        <td>{{ $activeStat['weighted'] ?? '' }}</td>

                                        {{-- Left Stats Columns --}}
                                        @php $leftStat = $pspData['leftStats'][$k] ?? null; @endphp
                                        <td>{{ $leftStat['group'] ?? '' }}</td>
                                        <td>{{ $leftStat['count'] ?? '' }}</td>
                                        <td>{{ isset($leftStat['percentage']) ? $leftStat['percentage'] . '%' : '' }}
                                        </td>
                                        <td>{{ $leftStat['weighted'] ?? '' }}</td>
                                    </tr>
                                @endfor
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>


        </div>

    </div>


    <div id="marksDefaultOffcanvas" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#marksDefaultOffcanvas_button" data-kt-drawer-close="#marksDefaultOffcanvas_close"
        data-kt-drawer-name="marksDefaultOffcanvas" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'350px', 'md': '768px'}" data-kt-drawer-direction="end">
        <div class="card card-body rounded-0 w-100 border-0 p-0">
            <table class="table table-row-bordered table-row-gray-500 align-middle gy-2 nowrap"
                id="retention-groups-table">
                <thead>
                    <tr>
                        <th><strong>Group</strong></th>
                        <th><strong>Min Days</strong></th>
                        <th><strong>Max Days</strong></th>
                        <th><strong>Points</strong></th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($allGroups as $group)
                        <tr>
                            <td>{{ $group->label }}</td>
                            <td>{{ $group->min }}</td>
                            <td>{{ $group->max }}</td>
                            <td>{{ $group->points }}</td>
                            <td>
                                <a href="{{ route('retention-groups.edit', $group->id) }}"
                                    class="btn btn-sm btn-primary py-1" target="_blank">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#retention_table').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    // deferRender: true,
                    // processing: true,
                    paging: false,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 2
                    },
                    columnDefs: [{
                            "className": 'py-2 border-end border-secondary td-border-left text-center',
                            "targets": [1]
                        },
                        {
                            "className": 'py-2 text-center',
                            "targets": '_all'
                        },
                    ],
                    language: {
                        "processing": "<div>Loading data, please wait...</div>"
                    },
                    oLanguage: {
                        "sSearch": ""
                    },
                    ordering: false,
                });

                $('#retention_table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title">Worker Retention & Attrition Analysis</h3>');

                $('#retention_table_wrapper > .card > .card-header > .card-toolbar > .toolbar-buttons').html(
                    '<button id="marksDefaultOffcanvas_button" class="btn btn-sm btn-secondary shadow">Defaults</button>'
                );
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#retention_sumamry_table').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    // deferRender: true,
                    // processing: true,
                    paging: false,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 2
                    },
                    columnDefs: [{
                            "className": 'py-2 border-end border-secondary td-border-left text-center',
                            "targets": [1]
                        },
                        {
                            "className": 'py-2 text-center',
                            "targets": '_all'
                        },
                    ],
                    language: {
                        "processing": "<div>Loading data, please wait...</div>"
                    },
                    oLanguage: {
                        "sSearch": ""
                    },
                    order: [
                        [1, 'asc'],
                    ]
                });

                $('#retention_sumamry_table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title">Summary</h3>');

                // $('#retention_table_wrapper > .card > .card-header > .card-toolbar > .toolbar-buttons').html(
                //     '<button id="marksDefaultOffcanvas_button" class="btn btn-sm btn-secondary shadow">Defaults</button>'
                // );
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#retention-groups-table').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    paging: false,
                    scrollX: true,
                    scrollY: "70vh",
                    scrollCollapse: true,
                    columnDefs: [{
                        "className": 'py-2 text-center',
                        "targets": '_all'
                    }, ],
                    language: {
                        "processing": "<div>Loading data, please wait...</div>"
                    },
                    oLanguage: {
                        "sSearch": ""
                    },
                    ordering: false,
                });

                $('#retention-groups-table_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title">Default Marks (Edit Below)</h3>');

            });
        </script>
    </x-slot>
</x-app-layout>
