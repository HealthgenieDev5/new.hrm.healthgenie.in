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
            <form id="update_shiftoverride" class="card" enctype="multipart/form-data" method="POST" action="{{ route('overrides.shiftoverride.update', $shiftoverride->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-3 mb-3">
                            <div class="form-floating">
                                <select name="worker_id" id="worker_id" class="form-select {{$errors->has('worker_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Worker">
                                    <option></option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ old('worker_id', $shiftoverride->worker_id) == $worker->id ? 'selected' : '' }}>
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
                                    <option value="{{ $shift->id }}" {{ old('shift_id', $shiftoverride->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                    <input type="text" id="start_date" class="form-control" name="start_date" value="{{ old('start_date', $shiftoverride->start_date) }}" placeholder="Start Date" data-input data-open>
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
                                    <input type="text" id="end_date" class="form-control" name="end_date" value="{{ old('end_date', $shiftoverride->end_date) }}" placeholder="End Date" data-input data-open>
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
                    altFormat: 'd M Y',
                    altInputClass: "form-control"
                })
            })
        </script>
    </x-slot>

</x-app-layout>