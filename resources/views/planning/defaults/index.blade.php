<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">

            <table class="table table-row-dashed table-row-gray-500 align-middle gy-4 nowrap " id="planningTable">
                <thead>
                    <tr>
                        <th class="text-dark text-ceter fw-bolder text-hover-primary fs-6 p-0">
                            <div class="text-dark text-center text-hover-primary px-3">
                                Line<br>
                                &nbsp;
                            </div>
                        </th>
                        <th class="text-dark text-ceter fw-bolder text-hover-primary fs-6 p-0">
                            <div class="text-dark text-center text-hover-primary px-3">
                                Parameter<br>
                                &nbsp;
                            </div>
                        </th>
                        @foreach ($dates as $date)
                        <th class="text-dark text-ceter fw-bolder text-hover-primary fs-6 p-0">
                            @php
                            $parsedDate = \Carbon\Carbon::parse($date);
                            $datePart = $parsedDate->format('d M');
                            $dayPart = $parsedDate->format('D');
                            $classes = 'text-dark text-center text-hover-primary px-3';
                            if ($parsedDate->isSunday()) {
                            $classes = 'text-white text-center bg-danger text-hover-primary px-3';
                            } elseif (in_array($parsedDate->format('Y-m-d'), $holidays)) {
                            $classes = 'text-white text-center bg-danger text-hover-primary px-3';
                            } elseif ($parsedDate->isToday()) {
                            $classes = 'text-white text-center bg-primary text-hover-primary px-3';
                            }
                            @endphp
                            <div class="{{ $classes }}">
                                {{ $datePart }}
                                <br>
                                {{ $dayPart }}
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php $firstTotal = true; @endphp
                    @foreach ($displayNames as $param => $label)
                    <tr style="background: #f8f9fa;">
                        <td><strong>@if ($firstTotal) Total @php $firstTotal = false; @endphp @endif</strong></td>
                        <td>{{ $label }}</td>
                        @foreach ($dates as $date)
                        <td class="text-center">
                            @if ($param === 'status')
                            --
                            @elseif ($param === 'shortfall')
                            @php
                            $shortfallDay = $totalSums['shortfall'][$date]['day'] ?? 0;
                            $excessDay = $totalSums['excess'][$date]['day'] ?? 0;
                            $shortfallNight = $totalSums['shortfall'][$date]['night'] ?? 0;
                            $excessNight = $totalSums['excess'][$date]['night'] ?? 0;
                            @endphp
                            @if ($shortfallDay > 0)
                            <span class="text-danger fw-bold">{{ $shortfallDay }}</span>
                            @elseif ($excessDay > 0)
                            <span class="text-success fw-bold">+{{ $excessDay }}</span>
                            @else
                            <span>0</span>
                            @endif
                            |
                            @if ($shortfallNight > 0)
                            <span class="text-danger fw-bold">{{ $shortfallNight }}</span>
                            @elseif ($excessNight > 0)
                            <span class="text-success fw-bold">+{{ $excessNight }}</span>
                            @else
                            <span>0</span>
                            @endif
                            @else
                            {{ $totalSums[$param][$date]['day'] }} | {{ $totalSums[$param][$date]['night'] }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    {{-- END TOTAL ROWS --}}
                    @foreach ($plans as $line => $parameters)
                    @php
                    $i = 1;
                    $displayNames = [
                    'total' => 'Required Total',
                    'workers' => 'Required Workers',
                    'extras' => 'Required Extras',
                    'supervisors' => 'Required Supervisors',
                    'qas' => 'Required Qas',
                    'services' => 'Required Services',
                    'movers' => 'Required Movers',
                    'assigned' => 'Assigned',
                    'present' => 'Present',
                    'shortfall' => 'Shortfall/Excess',
                    'status' => 'Status',
                    ];

                    $zeroDay = [];
                    $zeroNight = [];
                    foreach ($dates as $d) {
                    $zeroDay[$d] = ($parameters['total'][$d]['day'] ?? 0) == 0;
                    $zeroNight[$d] = ($parameters['total'][$d]['night'] ?? 0) == 0;
                    }
                    @endphp
                    @foreach ($parameters as $param => $values)
                    {{-- Skip the 'line_id', 'excess', and 'difference' rows --}}

                    @if ($param !== 'line_id' && $param !== 'excess' && $param !== 'difference')
                    <tr>
                        <td>
                            @if ($i == 1)
                            <strong>{{ $line }}</strong>
                            @elseif ($i == 2)
                            <a class="btn btn-sm btn-primary" target="_blank"
                                href="{{ route('production.planning.create', ['line_id' => $parameters['line_id']]) }}">
                                Open
                            </a>
                            @elseif ($i == 3)
                            <div class="d-flex justify-content-center">
                                <form method="POST" action="{{ route('production.planning.close-shift') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="line_id" value="{{ $parameters['line_id'] }}">
                                    <input type="hidden" name="shift_type" value="day">
                                    <button type="submit" class="btn btn-sm btn-danger">Close Day</button>
                                </form>
                            </div>
                            @elseif ($i == 4)
                            <div class="d-flex justify-content-center">
                                <form method="POST" action="{{ route('production.planning.close-shift') }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="line_id" value="{{ $parameters['line_id'] }}">
                                    <input type="hidden" name="shift_type" value="night">
                                    <button type="submit" class="btn btn-sm btn-dark">Close Night</button>
                                </form>
                            </div>
                            @else
                            {{ '' }}
                            @endif
                        </td>
                        {{-- <td>{{ ucfirst($param) }}</td> --}}
                        <td>{{ $displayNames[$param] ?? ucfirst($param) }}</td>
                        @foreach ($dates as $date)
                        @php
                        $val = $values[$date] ?? ['day' => 0, 'night' => 0];
                        $day = is_array($val) ? $val['day'] ?? 0 : $val;
                        $night = is_array($val) ? $val['night'] ?? 0 : null;
                        @endphp
                        <td class="text-center">
                            @if ($param === 'status')
                            @php
                            $dayStatus = is_array($val) ? ($val['day'] ?? 'ok') : $val;
                            $nightStatus = is_array($val) ? ($val['night'] ?? 'ok') : null;
                            @endphp
                            <span class="{{ $zeroDay[$date] ? 'text-danger' : ($dayStatus === 'short' ? 'text-danger fw-bold' : ($dayStatus === 'excess' ? 'text-success fw-bold' : '')) }}">
                                {{ $zeroDay[$date] ? 'Closed' : ucfirst($dayStatus) }}
                            </span>
                            @if ($night !== null)
                            |
                            <span class="{{ $zeroNight[$date] ? 'text-danger' : ($nightStatus === 'short' ? 'text-danger fw-bold' : ($nightStatus === 'excess' ? 'text-success fw-bold' : '')) }}">
                                {{ $zeroNight[$date] ? 'Closed' : ucfirst($nightStatus) }}
                            </span>
                            @endif
                            @elseif ($param === 'shortfall')
                            @php
                            $shortfallDay = $parameters['shortfall'][$date]['day'] ?? 0;
                            $excessDay = $parameters['excess'][$date]['day'] ?? 0;
                            $shortfallNight = $parameters['shortfall'][$date]['night'] ?? 0;
                            $excessNight = $parameters['excess'][$date]['night'] ?? 0;
                            @endphp
                            @if ($shortfallDay > 0)
                            <span class="text-danger fw-bold">{{ $shortfallDay }}</span>
                            @elseif ($excessDay > 0)
                            <span class="text-success fw-bold">+{{ $excessDay }}</span>
                            @else
                            <span>0</span>
                            @endif
                            @if ($night !== null)
                            |
                            @if ($shortfallNight > 0)
                            <span class="text-danger fw-bold">{{ $shortfallNight }}</span>
                            @elseif ($excessNight > 0)
                            <span class="text-success fw-bold">+{{ $excessNight }}</span>
                            @else
                            <span>0</span>
                            @endif
                            @endif
                            @else
                            @if ($night === null)
                            <span
                                class="{{ $zeroDay[$date] ? 'text-danger' : '' }}">{{ $day }}</span>
                            @else
                            <span
                                class="{{ $zeroDay[$date] ? 'text-danger' : '' }}">{{ $day }}</span>
                            |
                            <span
                                class="{{ $zeroNight[$date] ? 'text-danger' : '' }}">{{ $night }}</span>
                            @endif
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @php
                    $i++;
                    @endphp
                    @endif
                    @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->



    <x-slot name="scripts">
        {{-- <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script> --}}
        <script src="{{ asset('js/autoscroll-datatable.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#planningTable').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
                    paging: false,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    ordering: false,
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    deferRender: true,
                    processing: true,
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
                    createdRow: function(row, data, index) {
                        if ((index + 1) % 11 === 0) {
                            $(row).css("border-bottom", "5px solid red");
                        }
                        if (((index % 11) + 1) === 9) {
                            $(row).css('font-weight', 'bold');
                        }
                    },
                    initComplete: function() {
                        setTimeout(autoScrollToToday(
                            this.api(),
                            "<?= date('Y-m') ?>",
                            "<?= request()->get('yearmonth', date('Y-m')) ?>"
                        ), 500);
                    }
                });

                $('#planningTable_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title"><?= $pageTitle ?></h3>');

            });
        </script>

        <script>
            $(document).ready(function() {
                $('.worker_ids').select2({
                    width: '100%',
                    templateResult: formatWorkerOption,
                    templateSelection: formatWorkerSelection,
                    matcher: workerMatcher,
                    escapeMarkup: function(markup) {
                        return markup; // allow HTML
                    }
                });

                function formatWorkerOption(option) {
                    if (!option.id) return option.text;

                    const name = $(option.element).data('name');
                    const code = $(option.element).data('code');
                    const level = $(option.element).data('level');

                    return `
                        <div style="line-height: 1; display: flex; flex-direction: column; gap: 5px; padding: 2px 5px 2px 0px;">
                            <span>
                                <strong>${name}</strong> <small>(${code})</small>
                            </span>
                            <small>Level: ${level}</small>
                        </div>
                    `;
                }

                function formatWorkerSelection(option) {
                    if (!option.id) return option.text;

                    const name = $(option.element).data('name');
                    const code = $(option.element).data('code');
                    const level = $(option.element).data('level');

                    return `
                        <div style="line-height: 1; display: flex; flex-direction: column; gap: 5px; padding: 2px 5px 2px 0px;">
                            <span>
                                <strong>${name}</strong> <small>(${code})</small>
                            </span>
                            <small>Level: ${level}</small>
                        </div>
                    `;
                }

                function workerMatcher(params, data) {
                    if (!params.term || !data.element) return data;
                    const search = params.term.toLowerCase();
                    const name = String($(data.element).data('name') || '');
                    const code = String($(data.element).data('code') || '');
                    const level = String($(data.element).data('level') || '');
                    if (
                        name.toLowerCase().includes(search) ||
                        code.toLowerCase().includes(search) ||
                        level.toLowerCase().includes(search)
                    ) {
                        return data;
                    }
                    return null;
                }

                $(document).on("submit", ".addWorkersToLine", function(e) {
                    e.preventDefault();
                    const line_id = $(this).find('input[name="line_id"]').val();
                    const worker_ids = $(this).find('select[name="worker_ids[]"]').val();
                    const start_date = $(this).find('input[name="start_date"]').val();
                    const end_date = $(this).find('input[name="end_date"]').val();

                    addWorkersToLine(line_id, worker_ids, start_date, end_date);

                })

                function addWorkersToLine(line_id, worker_ids, start_date, end_date) {
                    $.ajax({
                        url: "{{ route('production.planning.assign-to-line') }}",
                        type: "POST",
                        data: {
                            worker_ids: worker_ids,
                            line_id: line_id,
                            start_date: start_date,
                            end_date: end_date,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log("Assigned successfully", response);
                        },
                        error: function(xhr, status, error) {
                            alert("Error: " + xhr.responseText || error);
                        }
                    });
                }
            })
        </script>


    </x-slot>

</x-app-layout>