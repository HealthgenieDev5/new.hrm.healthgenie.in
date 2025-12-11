<x-app-layout>
    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-xxl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Monthly Bonus History</h3>
                    <div class="card-tools">
                        <form action="{{ route('tenure-bonus.history') }}" method="GET" class="d-inline">
                            <div class="input-group mt-5">
                                <input type="text" name="month" id="month_picker" value="{{ $selectedMonthYear }}"
                                       class="form-control form-control-sm" >
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-primary btn-sm btn-primary">Filter</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Summary Cards -->
                    <div class="row mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-2" id="bonus_history_toolbar">
                            <div id="datatable-buttons-container"></div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-primary">
                                <div class="card-body">
                                    <h6 class="card-title">Total Bonuses</h6>
                                    <h3 class="card-text">{{ $summary['total_bonuses'] }}</h3>
                                    <p class="card-text">₹{{ number_format($summary['total_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-success">
                                <div class="card-body">
                                    <h6 class="card-title">Paid Bonuses</h6>
                                    <h3 class="card-text">{{ $summary['paid_bonuses'] }}</h3>
                                    <p class="card-text">₹{{ number_format($summary['paid_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light-warning">
                                <div class="card-body">
                                    <h6 class="card-title">Pending Bonuses</h6>
                                    <h3 class="card-text">{{ $summary['pending_bonuses'] }}</h3>
                                    <p class="card-text">₹{{ number_format($summary['pending_amount'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bonus History Table -->
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="bonus_history_table">
                            <thead>
                                <tr>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Worker</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Working Days</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Bonus Amount</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Bonus Date</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">PSP Worker</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Status</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Payment Details</th>
                                    <th class="text-dark fw-bolder text-hover-primar fs-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bonuses as $bonus)
                                    <tr>
                                        <td class="py-1">{{ $bonus->worker->name }}</td>
                                        <td class="py-1">{{ $bonus->working_days_completed }}</td>
                                        <td class="py-1">₹{{ number_format($bonus->bonus_amount, 2) }}</td>
                                        <td class="py-1">{{ $bonus->bonus_date->format('d-m-Y') }}</td>
                                        <td class="py-1">
                                            @if($bonus->is_psp_worker)
                                                <span class="badge badge-info">Yes</span>
                                            @else
                                                <span class="badge badge-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            @if($bonus->is_paid)
                                                <span class="badge badge-success">Paid</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            @if($bonus->is_paid)
                                                <small>
                                                    Paid on: {{ $bonus->paid_date->format('d-m-Y') }}<br>
                                                    Ref: {{ $bonus->payment_reference }}
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            @if(!$bonus->is_paid)
                                                <button type="button" class="btn btn-success btn-sm"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#markPaidModal{{ $bonus->id }}">
                                                    Mark as Paid
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    @if(!$bonus->is_paid)

                                        <div class="modal fade" id="markPaidModal{{ $bonus->id }}" tabindex="-1" aria-labelledby="markPaidModalLabel{{ $bonus->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('tenure-bonus.mark-paid', $bonus) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="markPaidModalLabel{{ $bonus->id }}">Mark Bonus as Paid</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="payment_reference_{{ $bonus->id }}" class="form-label">Payment Reference</label>
                                                                <input type="text" name="payment_reference" id="payment_reference_{{ $bonus->id }}" class="form-control" required>
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
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No bonuses found for this month</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $bonuses->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#month_picker').flatpickr({
                    dateFormat: 'Y-m',
                    altInput: true,
                    altFormat: 'M Y',
                    altInputClass: "form-control form-control-sm"
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#month_picker').flatpickr({
                    dateFormat: 'Y-m',
                    altInput: true,
                    altFormat: 'M Y',
                    altInputClass: "form-control form-control-sm"
                });

                // Initialize DataTable for sorting
                $('#bonus_history_table').DataTable({
                    paging: false, // Disable DataTables pagination since you use Laravel's
                    info: false,   // Hide table info
                    searching: false // Hide search box (optional)
                });
            });
        </script>
    </x-slot>
</x-app-layout>
