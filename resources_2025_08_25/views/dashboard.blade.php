<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-fluid">

            {{-- <div class="d-flex align-items-center justify-content-end gap-3 mb-5">
                <div class="btn-group">
                    <a href="#" class="btn btn-sm btn-danger">Absent Workmen</a>
                    <a href="#" class="btn btn-sm btn-dark">Attendance %</a>
                </div>
            </div> --}}

            <!-- Month Selection Form -->
            <form id="filter_form" class="card mb-3" method="GET" action="{{ route('dashboard.index') }}">
                <div class="card-body">
                    <div class="row gy-5">


                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker" id="yearmonth_picker" data-wrap="true">
                                    <input type="text" id="yearmonth" class="form-control form-control-sm"
                                        name="yearmonth" value="{{ request('yearmonth') ?? date('Y-m') }}"
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

            <table class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap " id="attendanceTable">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                        <th class="text-dark fw-bolder text-hover-primary fs-6">
                            @if (!in_array($column, ['PSP', 'Status', 'Max', 'Avg']))
                            @php
                            $parsedDate = \Carbon\Carbon::parse($column);
                            $datePart = $parsedDate->format('d M');
                            $dayPart = $parsedDate->format('D');
                            $classes = 'text-dark text-hover-primary px-3';
                            if ($parsedDate->isSunday()) {
                            $classes = 'text-white bg-danger text-hover-primary px-3';
                            } elseif (in_array($parsedDate->format('Y-m-d'), $holidays)) {
                            $classes = 'text-white bg-danger text-hover-primary px-3';
                            } elseif ($parsedDate->isToday()) {
                            $classes = 'text-white bg-primary text-hover-primary px-3';
                            }
                            @endphp
                            <div class="{{ $classes }}">
                                {{ $datePart }}<br>{{ $dayPart }}
                            </div>
                            @else
                            <div class="text-dark text-hover-primary px-3">
                                {{ $column }}<br><br>
                            </div>
                            @endif
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

                // $('#yearmonth_picker').flatpickr({
                //     dateFormat: 'Y-m',
                //     altInput: true,
                //     altFormat: 'M Y',
                //     altInputClass: "form-control form-control-sm"
                // });


                $('#yearmonth_picker').flatpickr({
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "Y-m",
                            altFormat: "F Y",
                            altInputClass: "form-control form-control-sm",
                            theme: "dark",
                        })
                    ]
                });

                $('#attendanceTable').DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons">>><"card-body py-0 px-3"rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',
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
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dashboard.attendance-summary') }}",
                        type: "GET",
                        data: function(d) {
                            d.yearmonth = $('#yearmonth').val();
                            d.shift_type = $('#shift_type').val();
                        }
                    },
                    columns: [{
                            data: 'psp',
                            name: 'PSP'
                        },
                        {
                            data: 'status',
                            name: 'Status'
                        },
                        {
                            data: 'max',
                            name: 'Max'
                        },
                        {
                            data: 'avg',
                            name: 'Avg'
                        },
                        <?php
                        foreach ($columns as $column) {
                            if (!in_array($column, ['PSP', 'Status', 'Max', 'Avg'])) {
                                echo "{data: '" . $column . "', name: '" . $column . "'},";
                            }
                        }
                        ?>
                    ],
                    paging: false,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    fixedColumns: {
                        left: 4
                    },
                    columnDefs: [{
                            "className": 'py-2 border-end border-secondary td-border-left text-center',
                            "targets": [3]
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
                    createdRow: function(row, data, index) {
                        if ((index + 1) % 12 === 0) {
                            $(row).css("border-bottom", "3px solid red");
                        }
                        if (((index % 12) + 1) === 10) {
                            $(row).css('background-color', '#ffcccc');
                        }
                        if (((index % 12) + 1) === 6) {
                            $(row).css({
                                'color': 'red',
                                'font-weight': 'bold'
                            });
                        }
                        if (
                            ((index % 12) + 1) === 2 ||
                            ((index % 12) + 1) === 8
                        ) {
                            $(row).css({
                                'color': '#000',
                                'font-weight': 'bold'
                            });
                        }

                        if (((index % 12) + 1) === 3) {
                            for (let i = 2; i < $('td', row).length; i++) {
                                let cell = $('td', row).eq(i);
                                let text = cell.text().trim();
                                if (text.includes('|')) {
                                    let parts = text.split('|').map(p => p.trim());
                                    let coloredParts = parts.map(part => {
                                        let num = parseFloat(part);
                                        let color = (num <= 0) ? 'green' : 'red';
                                        return `<span style="color: ${color}; ">${num}</span>`;
                                    });
                                    cell.html(coloredParts.join(' | '));
                                }
                            }
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

                $('#attendanceTable_wrapper > .card > .card-header > .card-title').replaceWith(
                    '<h3 class="card-title"><?= $pageTitle ?></h3>');

                $('#filterForm').submit(function(e) {
                    e.preventDefault();
                    $('#attendanceTable').DataTable().ajax.reload();
                });
            });
        </script>
    </x-slot>

</x-app-layout>