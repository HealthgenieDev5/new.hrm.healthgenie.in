<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-slot name="headerComponents">

    </x-slot>

     <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="create_shiftoverride" class="card" enctype="multipart/form-data" method="POST" action="{{ route('overrides.shiftoverride.store')}}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-3 mb-3">
                            <div class="form-floating">
                                <select name="worker_id" id="worker_id" class="form-select {{$errors->has('worker_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Worker">
                                    <option></option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ old('worker_id') == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }} ({{ $worker->code }})
                                    </option>
                                    @endforeach
                                </select>
                                <label for="worker_id" class="required form-label">Worker</label>
                            </div>
                            <small class="text-danger d-block" id="worker_id_error">{{ $errors->first('worker_id') ?? '' }}</small>
                        </div>

                        <div class="col-lg-3 mb-3">
                            <div class="form-floating">
                                <select name="shift_id" id="shift_id" class="form-select {{$errors->has('shift_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Shift">
                                    <option></option>
                                    @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="shift_id" class="required form-label">Shift</label>
                            </div>
                            <small class="text-danger d-block" id="shift_id_error">{{ $errors->first('shift_id') ?? '' }}</small>
                        </div>

                        <div class="col-lg-3 mb-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker {{$errors->has('start_date') ? 'is-invalid' : '' }}" id="start_date_picker" data-wrap="true">
                                    <input type="text" id="start_date" class="form-control" name="start_date" value="{{ old('start_date') }}" placeholder="Start Date" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <label class="required form-label">Start Date</label>
                            </div>
                            <small class="text-danger d-block" id="code_error">{{ $errors->first('start_date') ?? '' }}</small>
                        </div>

                        <div class="col-lg-3 mb-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker {{$errors->has('end_date') ? 'is-invalid' : '' }}" id="end_date_picker" data-wrap="true">
                                    <input type="text" id="end_date" class="form-control" name="end_date" value="{{ old('end_date') }}" placeholder="End Date" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <label class="required form-label">End Date</label>
                            </div>
                            <small class="text-danger d-block" id="code_error">{{ $errors->first('end_date') ?? '' }}</small>
                        </div>
                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-sm btn-success">
                        <span class="menu-icon">
                            <i class="fa fa-check">
                            </i>
                        </span>
                        <span class="menu-title">Submit</span>
                    </button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->

            <!--begin::Datatable Card-->
            <div class="card mt-5" id="shift_overrides_card" style="display: none;">
                <div class="card-header">
                    <h3 class="card-title">Existing Shift Overrides</h3>
                </div>
                <div class="card-body">
                    <table id="shift_overrides_table" class="table table-row-dashed table-row-gray-300 align-middle gy-4">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">Worker</th>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">Shift</th>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">Start From</th>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">Ends On</th>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Datatable Card-->

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->



    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('.input-group-flatpicker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: "form-control"
                })

                // Initialize DataTable
                var shiftOverridesTable = null;

                // Worker dropdown change event
                $('#worker_id').on('change', function() {
                    var workerId = $(this).val();

                    if (workerId) {
                        // Show the datatable card
                        $('#shift_overrides_card').show();

                        // Destroy existing datatable if it exists
                        if (shiftOverridesTable) {
                            shiftOverridesTable.destroy();
                        }

                        // Initialize datatable with worker filter
                        shiftOverridesTable = $('#shift_overrides_table').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": {
                                url: "{{ route('overrides.shiftoverride.getShiftOverrides') }}",
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: function(d) {
                                    d.worker_id = workerId;
                                }
                            },
                            "columns": [
                                {
                                    data: 'worker_name',
                                    name: 'worker_name'
                                },
                                {
                                    data: 'shift_name',
                                    name: 'shift_name'
                                },
                                {
                                    data: 'start_date',
                                    name: 'start_date'
                                },
                                {
                                    data: 'end_date',
                                    name: 'end_date'
                                },
                                {
                                    data: 'action',
                                    name: 'action',
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            "order": [[2, 'desc']],
                            "pageLength": 10,
                            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                            "language": {
                                "processing": "<div>Loading shift overrides...</div>",
                                "emptyTable": "No shift overrides found for this worker",
                                "zeroRecords": "No matching shift overrides found"
                            }
                        });
                    } else {
                        // Hide the datatable card if no worker is selected
                        $('#shift_overrides_card').hide();

                        // Destroy the datatable
                        if (shiftOverridesTable) {
                            shiftOverridesTable.destroy();
                            shiftOverridesTable = null;
                        }
                    }
                });

                // Trigger change event on page load if worker is already selected
                if ($('#worker_id').val()) {
                    $('#worker_id').trigger('change');
                }

                // Delete shift override via AJAX
                $(document).on('click', '.delete-shift-override', function() {
                    if (confirm('Are you sure you want to delete this shift override?')) {
                        var deleteBtn = $(this);
                        var deleteUrl = deleteBtn.data('url');

                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Reload the datatable
                                if (shiftOverridesTable) {
                                    shiftOverridesTable.ajax.reload();
                                }

                                // Show success message (you can customize this)
                                alert('Shift Override deleted successfully.');
                            },
                            error: function(xhr) {
                                alert('Error deleting shift override. Please try again.');
                            }
                        });
                    }
                });
            })
        </script>
    </x-slot>

</x-app-layout>