<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-slot name="headerComponents">

    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="edit_holiday" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.holidays.update', $holiday->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-4 col-md-6 mb-3">

                            <div class="row gy-5">
                                <div class="col-lg-12 mb-3">
                                    <label class="required form-label">Holiday Name</label>
                                    <input type="text" name="name" value="{{ old('name', $holiday->name) }}" class="form-control form-control-solid" placeholder="Name" />
                                    @if ($errors->has('name'))
                                    <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                                    @endif
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Date</label>
                                    <div class="input-group input-group-solid input-group-flatpicker" id="date_picker" data-wrap="true">
                                        <input type="text" id="date" class="form-control" name="date" placeholder="Date" value="{{ old('date', $holiday->date) }}" data-input data-open>
                                        <span class="input-group-text cursor-pointer" data-toggle>
                                            <i class="far fa-calendar-alt"></i>
                                        </span>
                                    </div>
                                    <!--begin::Error Message-->
                                    @if ($errors->has('date'))
                                    <small class="text-danger d-block">{{ $errors->first('date') }}</small>
                                    @endif
                                    <!--end::Error Message-->
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <label class="required form-label">Code</label>
                                    <select name="code" class="form-select form-select-solid" data-control="select2" data-placeholder="Select Code">
                                        <option></option>
                                        <option value="HL" {{ old('code', $holiday->code ?? '') == 'HL' ? 'selected' : '' }}>HL</option>
                                        <option value="NH" {{ old('code', $holiday->code ?? '') == 'NH' ? 'selected' : '' }}>NH</option>
                                        <option value="RH" {{ old('code', $holiday->code ?? '') == 'RH' ? 'selected' : '' }}>RH</option>
                                        <option value="SPL HL" {{ old('code', $holiday->code ?? '') == 'SPL HL' ? 'selected' : '' }}>SPL HL</option>
                                    </select>
                                    <!--begin::Error Message-->
                                    @if ($errors->has('code'))
                                    <small class="text-danger d-block">{{ $errors->first('code') }}</small>
                                    @endif
                                    <!--end::Error Message-->
                                </div>

                            </div>

                        </div>

                        @if($holiday->code == 'SPL HL')
                        <div class="col-lg-8 col-md-6 mb-3">
                            <div class="row gy-5">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Assign to Workers</label>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <div class="mb-3">
                                        <label for="worker-list" class="form-label">Assign Workers</label>
                                        <div class="mb-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid border rounded p-2 pe-4 bg-light-primary d-inline-block">
                                                <input class="form-check-input" type="checkbox" id="select_all_workers">
                                                <label class="form-check-label fw-bold" for="select_all_workers">Select All</label>
                                            </div>
                                        </div>
                                        <div id="worker-list">
                                            @foreach($workers as $divisionId => $divisionWorkers)
                                                @php
                                                    $divisionName = $divisionId == 0 ? 'Other' : ($divisionWorkers->first()->division->name ?? 'Other');
                                                @endphp
                                                <div class="division-group mb-3" data-division_id="{{ $divisionId }}">
                                                    <div class="border-bottom pb-1 mb-2 d-flex align-items-center">
                                                        <div class="form-check form-check-sm form-check-custom form-check-solid" id="division-list">
                                                            <input class="form-check-input division-checkbox" type="checkbox" value="{{ $divisionId }}" id="division_{{ $divisionId }}">
                                                            <label class="form-check-label fw-bold text-primary" for="division_{{ $divisionId }}">{{ $divisionName }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-5 flex-wrap ps-3">
                                                        @foreach($divisionWorkers as $worker)
                                                        <div class="form-check bg-slate-400 border rounded-xl ps-12 pe-3 py-1 d-flex gap-3" data-division_id="{{ $divisionId }}">
                                                            <input class="form-check-input" type="checkbox" name="workers[]" value="{{ $worker->id }}" id="worker_{{ $worker->id }}" {{ $worker->holidays->contains('id', $holiday->id) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="worker_{{ $worker->id }}">{{ $worker->name }} ({{ $worker->code }})</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                        <span class="menu-title">Update</span>
                    </button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->


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

                // Function to update division checkbox based on its workers
                function updateDivisionCheckbox(divisionId) {
                    let totalInDivision = $('#worker-list .form-check[data-division_id="' + divisionId + '"] input').length;
                    let checkedInDivision = $('#worker-list .form-check[data-division_id="' + divisionId + '"] input:checked').length;
                    $('.division-checkbox[value="' + divisionId + '"]').prop('checked', totalInDivision > 0 && totalInDivision === checkedInDivision);
                }

                // Function to update Select All checkbox
                function updateSelectAllCheckbox() {
                    let totalWorkers = $('#worker-list .form-check[data-division_id] input').length;
                    let checkedWorkers = $('#worker-list .form-check[data-division_id] input:checked').length;
                    $('#select_all_workers').prop('checked', totalWorkers > 0 && totalWorkers === checkedWorkers);
                }

                // Select All Workers checkbox - also selects all divisions
                $('#select_all_workers').on('change', function() {
                    let isChecked = $(this).prop('checked');
                    $('#worker-list .form-check[data-division_id] input').prop('checked', isChecked);
                    $('.division-checkbox').prop('checked', isChecked);
                });

                // Update Select All and Division checkboxes when individual worker checkbox changes
                $(document).on('change', '#worker-list .form-check[data-division_id] input', function() {
                    let divisionId = $(this).closest('.form-check').data('division_id');
                    updateDivisionCheckbox(divisionId);
                    updateSelectAllCheckbox();
                });

                // Initialize checkbox states on page load
                (function() {
                    // Update each division checkbox based on its workers
                    $('.division-checkbox').each(function() {
                        updateDivisionCheckbox($(this).val());
                    });
                    // Update Select All checkbox
                    updateSelectAllCheckbox();
                })();

                $(document).on('change', '.division-checkbox', function() {
                    let $selected_division = $(this).val();

                    if ($(this).prop('checked')) {
                        // Select all workers belonging to the selected division
                        $('#worker-list .form-check[data-division_id="' + $selected_division + '"] input').prop('checked', true);
                    } else {
                        // Deselect all workers belonging to the unselected division
                        $('#worker-list .form-check[data-division_id="' + $selected_division + '"] input').prop('checked', false);
                    }
                    // Update Select All checkbox
                    updateSelectAllCheckbox();
                });

            })
        </script>


    </x-slot>

</x-app-layout>