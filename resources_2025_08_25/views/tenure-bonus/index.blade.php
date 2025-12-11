<x-app-layout>
    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">


            <!-- Summary Cards -->
            <div class="row mb-5">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light-primary m-2">
                            <h6 class="card-title">Total Bonuses</h6>
                            <h3 class="card-text">{{ $summary['total_bonuses'] }}</h3>
                            <p class="card-text">₹{{ number_format($summary['total_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light-success m-2">
                            <h6 class="card-title">Paid Bonuses</h6>
                            <h3 class="card-text">{{ $summary['paid_bonuses'] }}</h3>
                            <p class="card-text">₹{{ number_format($summary['paid_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 ">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light-warning m-2">
                            <h6 class="card-title">Pending Bonuses</h6>
                            <h3 class="card-text">{{ $summary['pending_bonuses'] }}</h3>
                            <p class="card-text">₹{{ number_format($summary['pending_amount'], 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light-danger m-2">
                            <h6 class="card-title">Unpaid Bonuses</h6>
                            <h3 class="card-text">{{ $summary['unpaid_bonuses'] ?? 0 }}</h3>
                            <p class="card-text">₹{{ number_format($summary['unpaid_amount'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body bg-light-info m-2">
                            <h6 class="card-title">PSP Worker Bonuses</h6>
                            <h3 class="card-text">{{ $summary['psp_bonuses'] }}</h3>
                            <p class="card-text">₹{{ number_format($summary['psp_amount'], 2) }}</p>
                        </div>
                    </div>
                </div> --}}

            </div>


            <!-- Bonus History Table -->
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="bonus_history_table">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="select-all header-select-all"></th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Worker</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Working Days</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Incentive Amount</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Completion Date</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">PSP Name</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Status</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Payment Details</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><input type="checkbox" class="select-all footer-select-all"></th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Worker</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Working Days</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Incentive Amount</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Completion Date</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">PSP Name</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Status</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Payment Details</th>
                        <th class="text-dark fw-bolder text-hover-primary fs-6">Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                    @forelse($bonuses as $bonus)
                        <tr>
                            <td>
                                <input type="checkbox" class="row-checkbox" value="{{ $bonus->id }}">
                            </td>
                            <td class="py-1">{{ $bonus->worker->name . ' (' . $bonus->worker->code . ')' }}</td>
                            <td class="py-1">{{ $bonus->working_days_completed }}</td>
                            <td class="py-1">₹{{ number_format($bonus->bonus_amount, 2) }}</td>
                            <td class="py-1">{{ $bonus->actual_eligibility_date->format('d-m-Y') }}</td>
                            <td class="py-1">
                                {{-- @if ($bonus->is_psp_worker)
                                    <span class="badge badge-info">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif --}}
                                {{ $bonus->worker->psp->name ?? '-' }}
                            </td>
                            <td class="py-1">
                                @if ($bonus->is_paid)
                                    <span class="badge badge-success">Paid</span>
                                @elseif ($bonus->payment_reference && str_contains($bonus->payment_reference, 'Unpaid'))
                                    <span class="badge badge-danger">Unpaid</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td class="py-1">
                                @if ($bonus->is_paid)
                                    <small>
                                        Paid on: {{ $bonus->paid_date->format('d-m-Y') }}<br>
                                        Ref: {{ $bonus->payment_reference }}
                                    </small>
                                @elseif ($bonus->payment_reference && str_contains($bonus->payment_reference, 'Unpaid'))
                                    <small class="text-danger">
                                        Unpaid: {{ $bonus->payment_reference }}
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-1">
                                @if (!$bonus->is_paid && !($bonus->payment_reference && str_contains($bonus->payment_reference, 'Unpaid')))
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success btn-sm mark-as-paid-button"
                                            data-bonus_id = "{{ $bonus->id }}">
                                            Mark as Paid</button>
                                        <button type="button" class="btn btn-danger btn-sm mark-as-unpaid-button"
                                            data-bonus_id = "{{ $bonus->id }}">
                                            Mark as Unpaid</button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No bonuses found for this month</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- {{ $bonuses->links() }} --}}
        </div>
    </div>

    {{-- Modal for Marking Bonus as Paid --}}
    <div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="mark_paid_form" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="markPaidModalLabel">Mark Bonus as Paid</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="payment_reference" class="form-label">Payment Reference</label>
                            <input type="text" name="payment_reference" id="payment_reference" class="form-control"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal for Marking Bonus as Unpaid --}}
    <div class="modal fade" id="markUnpaidModal" tabindex="-1" aria-labelledby="markUnpaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="mark_unpaid_form" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="markUnpaidModalLabel">Mark Bonus as Unpaid</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="unpaid_reason" class="form-label">Reason for Not Paying</label>
                            <select name="unpaid_reason" id="unpaid_reason" class="form-control" required>
                                <option value="">Select a reason</option>
                                <option value="employee_left">Employee Left</option>
                                <option value="terminated">Terminated</option>
                                <option value="contract_ended">Contract Ended</option>
                                <option value="performance_issues">Performance Issues</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="unpaid_notes" class="form-label">Additional Notes</label>
                            <textarea name="unpaid_notes" id="unpaid_notes" class="form-control" rows="3" placeholder="Provide additional details..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Confirm Unpaid</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Drawer for Bonus Calculation Rules --}}
    <div id="rules_drawer" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#rules_drawer_trigger" data-kt-drawer-close="#rules_drawer_close"
        data-kt-drawer-width="500px">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bonus Calculation Rules</h3>
                <div class="card-toolbar">
                    <button id="rules_drawer_close" class="btn btn-icon btn-sm btn-active-light-primary"
                        data-kt-drawer-dismiss="true">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">Working Days Range</th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">Bonus Amount</th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">Type</th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bonusRules as $rule)
                                <tr>
                                    <td class="py-1">{{ $rule->working_days_start }} -
                                        {{ $rule->working_days_end }}</td>
                                    <td class="py-1">₹{{ number_format($rule->bonus_amount, 2) }}</td>
                                    <td class="py-1">
                                        {{ $rule->is_completion_bonus ? 'Completion Bonus' : 'Regular Bonus' }}</td>
                                    <td class="py-1">
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <style>
        input.btn-max-width {
            max-width: 100px;
        }
    </style>
    {{-- Scripts --}}
    <x-slot name="scripts">

        <script>
            $(document).ready(function() {
                function initializeMonthPicker() {
                    $('#month_picker').flatpickr({
                        dateFormat: 'Y-m',
                        altInput: true,
                        altFormat: 'M Y',
                        altInputClass: "btn btn-secondary btn-sm border border-primary btn-max-width",
                        onChange: function(selectedDates, dateStr, instance) {
                            var monthYear = dateStr; // e.g., "2023-10"
                            var formAction = "{{ route('reports.tenure-bonus.index') }}?month=" +
                                monthYear;
                            window.location.href = formAction;
                        }
                    });
                }

                initializeMonthPicker();

                $(document).on('click', '.mark-as-paid-button', function() {
                    markBonusAsPaid(this);
                });

                $(document).on('click', '.mark-as-unpaid-button', function() {
                    markBonusAsUnpaid(this);
                });

                function markBonusAsPaid(button) {
                    var bonusId = $(button).data('bonus_id');
                    var formAction = "{{ route('reports.tenure-bonus.mark-paid', ':bonus') }}".replace(':bonus',
                        bonusId);
                    $('#mark_paid_form').attr('action', formAction);
                    $('#markPaidModal').modal('show');
                }

                function markBonusAsUnpaid(button) {
                    var bonusId = $(button).data('bonus_id');
                    var formAction = "{{ route('reports.tenure-bonus.mark-unpaid', ':bonus') }}".replace(':bonus',
                        bonusId);
                    $('#mark_unpaid_form').attr('action', formAction);
                    $('#markUnpaidModal').modal('show');
                }

                $("#bonus_history_table").DataTable({
                    dom: '<"card"<"card-header"<"card-title"><"card-toolbar"<"datatable-buttons-container me-1"B><"toolbar-buttons"f>>><"card-body py-0 "rt><"card-footer pt-5 pb-3"<"row"<"col-sm-12 col-md-5 d-flex align-items-center justify-content-start mb-3 mb-md-0"li><"col-sm-12 col-md-7 d-flex align-items-center justify-content-start justify-content-md-end php-pagination-container"p>>>>',

                    paging: false,
                    scrollX: true,
                    scrollY: "60vh",
                    scrollCollapse: true,
                    ordering: true,
                    order: [
                        [3, 'desc']
                    ],
                    columnDefs: [{
                            orderable: false,
                            targets: 0
                        } // Disable sorting for checkbox column
                    ],
                    buttons: [{
                        extend: 'excel',
                        text: '<i class="fa-solid fa-file-excel"></i> Excel',
                        className: 'btn-sm border border-primary',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    oLanguage: {
                        "sSearch": ""
                    },
                    initComplete: function() {
                        setTimeout(function() {
                            $('#bonus_history_table_wrapper > .card > .card-header > .card-title')
                                .replaceWith(
                                    `<div class="d-flex align-items-center justify-content-start">
                                        <h3 class="card-title"><?= $pageTitle ?></h3>
                                        
                                    </div>`
                                );

                            $('#bonus_history_table_wrapper .dt-buttons').append(
                                '<button id="rules_drawer_trigger" type="button" class="btn btn-secondary btn-sm border border-primary">See Rules</button>'
                            );

                            $('#bonus_history_table_wrapper .dt-buttons').append(
                                `<input type="text" name="month" id="month_picker" value="{{ $selectedMonthYear }}" class="form-control form-control-sm" >`
                            );

                            $('#bonus_history_table_wrapper .dt-buttons').prepend(
                                `<select id="datatable_actions" class="btn btn-secondary btn-sm border border-primary"><option value="">Actions</option><option value="Mark as Paid">Mark as Paid</option><option value="Mark as Unpaid">Mark as Unpaid</option></select>`
                            );

                            initializeMonthPicker();
                            $('#bonus_history_table_wrapper .toolbar-buttons input[type="search"]')
                                .attr('placeholder', 'Search...');

                        }, 500);
                    }
                });


                $(document).on('change', '.select-all', function() {
                    var $selectAll = $(this);
                    if ($selectAll.is(':checked')) {
                        $('.row-checkbox').prop('checked', true);
                    } else {
                        $('.row-checkbox').prop('checked', false);
                    }
                    if ($(this).hasClass('header-select-all')) {
                        $('.footer-select-all').prop('checked', $selectAll.is(':checked'));
                    } else {
                        $('.header-select-all').prop('checked', $selectAll.is(':checked'));
                    }
                });

                $(document).on('change', '.row-checkbox', function() {
                    var allChecked = $('.row-checkbox:checked').length === $('.row-checkbox').length;
                    $('.select-all').prop('checked', allChecked);
                });

                $(document).on('change', '#datatable_actions', function() {
                    let action = $(this).val();
                    if (action === 'Mark as Paid') {
                        let selectedRows = $('.row-checkbox:checked');
                        if (selectedRows.length > 0) {
                            var ids = [];
                            selectedRows.each(function() {
                                ids.push($(this).val());
                            });

                            let actionUrl = "{{ route('reports.tenure-bonus.mark-paid-bulk') }}";
                            let csrfToken = '{{ csrf_token() }}';

                            // Send POST request with AJAX
                            $.ajax({
                                url: actionUrl,
                                method: 'POST',
                                data: {
                                    keys: ids,
                                    _token: csrfToken // Required for Laravel
                                },
                                success: function(response) {
                                    alert(response.message || 'Marked as paid!');
                                    $('.row-checkbox').each(function() {
                                        $(this).prop('checked', false);
                                    });
                                    location.reload();
                                },
                                error: function(xhr) {
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        alert(xhr.responseJSON.message);
                                    } else {
                                        alert('Something went wrong.');
                                    }
                                }
                            });

                        } else {
                            alert("Please select at least one row.");
                        }
                    } else if (action === 'Mark as Unpaid') {
                        let selectedRows = $('.row-checkbox:checked');
                        if (selectedRows.length > 0) {
                            var ids = [];
                            selectedRows.each(function() {
                                ids.push($(this).val());
                            });

                            let actionUrl = "{{ route('reports.tenure-bonus.mark-unpaid-bulk') }}";
                            let csrfToken = '{{ csrf_token() }}';

                            // Send POST request with AJAX
                            $.ajax({
                                url: actionUrl,
                                method: 'POST',
                                data: {
                                    keys: ids,
                                    _token: csrfToken // Required for Laravel
                                },
                                success: function(response) {
                                    alert(response.message || 'Marked as unpaid!');
                                    $('.row-checkbox').each(function() {
                                        $(this).prop('checked', false);
                                    });
                                    location.reload();
                                },
                                error: function(xhr) {
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        alert(xhr.responseJSON.message);
                                    } else {
                                        alert('Something went wrong.');
                                    }
                                }
                            });

                        } else {
                            alert("Please select at least one row.");
                        }
                    }
                    
                    // Reset the dropdown
                    $(this).val('');
                });
            });
        </script>
    </x-slot>
</x-app-layout>
