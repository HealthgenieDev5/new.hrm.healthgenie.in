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
            <form id="update_manualpunching" class="card" enctype="multipart/form-data" method="POST" action="{{ route('overrides.manualpunching.update', $manualPunching->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-4 mb-3">
                            <div class="mb-3">
                                <div class="form-floating">
                                    <select name="worker_id" id="worker_id" class="form-select {{$errors->has('worker_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Worker">
                                        <option></option>
                                        @foreach($workers as $worker)
                                        <option value="{{ $worker->id }}" {{ old('worker_id', $manualPunching->worker_id) == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->name }} ({{ $worker->code }})
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="worker_id" class="required form-label">Worker</label>
                                </div>
                                <small class="text-danger d-block" id="worker_id_error">{{ $errors->first('worker_id') ?? '' }}</small>
                            </div>
                        </div>

                        <div class="col-lg-8 mb-3">

                            <div class="row">

                                <div class="col-lg-6 mb-3">
                                    <div class="mb-7">
                                        <div class="form-floating">
                                            <select name="shift_type" id="shift_type" class="form-select {{$errors->has('shift_type') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Shift Type">
                                                <option></option>
                                                <option value="day" {{ old('shift_type', $manualPunching->shift_type) == 'day' ? 'selected' : '' }}>
                                                    Day
                                                </option>
                                                <option value="night" {{ old('shift_type', $manualPunching->shift_type) == 'night' ? 'selected' : '' }}>
                                                    Night
                                                </option>
                                            </select>
                                            <label for="shift_type" class="required form-label">Shift Type</label>
                                        </div>
                                        <small class="text-danger d-block" id="shift_type_error">{{ $errors->first('shift_type') ?? '' }}</small>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="mb-7">
                                        <div class="form-floating-flatpickr">
                                            <div class="input-group input-group-flatpicker {{$errors->has('date') ? 'is-invalid' : '' }}" id="date_picker" data-wrap="true">
                                                <input type="text" id="date" class="form-control" name="date" value="{{ old('date', $manualPunching->date) }}" placeholder="Date" data-input data-open>
                                                <span class="input-group-text cursor-pointer" data-toggle>
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <label class="required form-label">Date</label>
                                        </div>
                                        <small class="text-danger d-block" id="date_error">{{ $errors->first('date') ?? '' }}</small>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="mb-7">
                                        <div class="form-floating-flatpickr">
                                            <div class="input-group input-group-flatpicker" id="intime_picker" data-wrap="true">
                                                <input type="text" id="intime" class="form-control" name="intime" value="{{ old('intime', $manualPunching->intime) }}" placeholder="Punch In" data-input data-open>
                                                <span class="input-group-text cursor-pointer" onclick="clearInTimePicker()">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                                <span class="input-group-text cursor-pointer" data-toggle>
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <label class="required form-label">Punch In</label>
                                        </div>
                                        <small class="text-danger d-block" id="intime_error">{{ $errors->first('intime') ?? '' }}</small>
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-3">
                                    <div class="mb-7">
                                        <div class="form-floating-flatpickr">
                                            <div class="input-group input-group-flatpicker" id="outtime_picker" data-wrap="true">
                                                <input type="text" id="outtime" class="form-control" name="outtime" value="{{ old('outtime', $manualPunching->outtime) }}" placeholder="Punch Out" data-input data-open>
                                                <span class="input-group-text cursor-pointer" onclick="clearOutTimePicker()">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                                <span class="input-group-text cursor-pointer" data-toggle>
                                                    <i class="far fa-calendar-alt"></i>
                                                </span>
                                            </div>
                                            <label class="required form-label">Punch Out</label>
                                        </div>
                                        <small class="text-danger d-block" id="outtime_error">{{ $errors->first('outtime') ?? '' }}</small>
                                    </div>
                                </div>

                                <div class="col-lg-12 mb-3">
                                    <div class="mb-7">
                                        <div class="form-floating">
                                            <textarea class="form-control form-control" name="remarks" placeholder="Enter remarks">{{ old('remakrs', $manualPunching->remarks) }}</textarea>
                                            <label class="required form-label">Remarks</label>
                                        </div>
                                        @if ($errors->has('remarks'))
                                        <small class="text-danger d-block">{{ $errors->first('remarks') }}</small>
                                        @endif
                                    </div>
                                </div>

                            </div>

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
                $('#date_picker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd M, Y',
                    altInputClass: "form-control"
                });

                window.intime_picker = $('#intime_picker').flatpickr({
                    dateFormat: "H:i",
                    altInput: true,
                    altFormat: "h:i K",
                    altInputClass: "form-control",
                    enableTime: true,
                    noCalendar: true
                });

                window.outtime_picker = $('#outtime_picker').flatpickr({
                    dateFormat: "H:i",
                    altInput: true,
                    altFormat: "h:i K",
                    altInputClass: "form-control",
                    enableTime: true,
                    noCalendar: true
                });
            })

            function clearInTimePicker() {
                window.intime_picker.setDate(null, true); // Clears Flatpickr and updates UI
            }

            function clearOutTimePicker() {
                window.outtime_picker.setDate(null, true); // Clears Flatpickr and updates UI
            }
        </script>
    </x-slot>

</x-app-layout>