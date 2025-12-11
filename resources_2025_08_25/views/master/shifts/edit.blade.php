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
            <form id="edit_shift" class="card" enctype="multipart/form-data" method="POST"
                action="{{ route('master.shifts.update', $shift->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">

                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">shift Name</label>
                            <input type="text" name="name" value="{{ old('name', $shift->name) }}"
                                class="form-control form-control-solid" placeholder="Name" />
                            @if ($errors->has('name'))
                            <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">In time</label>
                            <input id="in_time" type="time" name="in_time"
                                value="{{ old('in_time', \Carbon\Carbon::parse($shift->in_time)->format('H:i')) }}"
                                class="form-control form-control-solid" />
                            @if ($errors->has('in_time'))
                            <small class="text-danger d-block">{{ $errors->first('in_time') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">Out time</label>
                            <input id="out_time" type="time" name="out_time"
                                value="{{ old('out_time', \Carbon\Carbon::parse($shift->out_time)->format('H:i')) }}"
                                class="form-control form-control-solid" />
                            @if ($errors->has('out_time'))
                            <small class="text-danger d-block">{{ $errors->first('out_time') }}</small>
                            @endif
                        </div>


                    </div>

                    <div class="row gy-5">

                        <div class="col-lg-2 col-md-3 mb-3">
                            <div class="">
                                <label class="required form-label">Grace minutes</label>
                                <div class="input-group input-group-flatpicker" id="grace_minutes_picker" data-wrap="true">
                                    <input type="text" id="grace_minutes" class="form-control" name="grace_minutes" value="{{ old('grace_minutes', $shift->grace_minutes) }}" placeholder="Grace minutes" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                            <small class="text-danger d-block" id="grace_minutes_error">{{ $errors->first('grace_minutes') ?? '' }}</small>
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">Week Off</label>
                            <select name="week_off" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Week off">
                                <option></option>
                                <option value="Sunday" {{ old('week_off', $shift->week_off ?? '') == 'Sunday' ?
                                    'selected' : '' }}>Sunday</option>
                                <option value="Monday" {{ old('week_off', $shift->week_off ?? '') == 'Monday' ?
                                    'selected' : '' }}>Monday</option>
                                <option value="Tuesday" {{ old('week_off', $shift->week_off ?? '') == 'Tuesday' ?
                                    'selected' : '' }}>Tuesday
                                </option>
                                <option value="Wednesday" {{ old('week_off', $shift->week_off ?? '') == 'Wednesday'
                                    ? 'selected' : '' }}>Wednesday
                                </option>
                                <option value="Thursday" {{ old('week_off', $shift->week_off ?? '') == 'Thursday' ?
                                    'selected' : '' }}>Thursday
                                </option>
                                <option value="Friday" {{ old('week_off', $shift->week_off ?? '') == 'Friday' ?
                                    'selected' : '' }}>Friday</option>
                                <option value="Saturday" {{ old('week_off', $shift->week_off ?? '') == 'Saturday' ?
                                    'selected' : '' }}>Saturday
                                </option>
                            </select>
                            @if ($errors->has('week_off'))
                            <small class="text-danger d-block">{{ $errors->first('week_off') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">Sub Type</label>
                            <select name="sub_type" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Sub Type">
                                <option></option>
                                <option value="regular" {{ old('sub_type', $shift->sub_type ?? '') == 'regular' ?
                                    'selected' : '' }}>Regular</option>
                                <option value="reduced" {{ old('sub_type', $shift->sub_type ?? '') == 'reduced' ?
                                    'selected' : '' }}>Reduced</option>
                            </select>
                            @if ($errors->has('sub_type'))
                            <small class="text-danger d-block">{{ $errors->first('sub_type') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">Sister Shift</label>
                            <select name="sister_shift" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select Sister Shift">
                                <option></option>

                                @foreach($shifts as $dbShift)
                                <option value="{{ $dbShift->id }}" {{ old('sister_shift', $shift?->sisterShift?->id) == $dbShift->id ? 'selected' : '' }}>
                                    {{ $dbShift->name }}
                                </option>
                                @endforeach

                            </select>
                            @if ($errors->has('sister_shift'))
                            <small class="text-danger d-block">{{ $errors->first('sister_shift') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-2 col-md-3 mb-3">
                            <label class="required form-label">Is Internal</label>
                            <select name="is_internal" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="is Internal">
                                <option></option>
                                <option value="0" {{ old('is_internal', $shift->is_internal ?? '') == '0' ?
                                    'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_internal', $shift->is_internal ?? '') == '1' ?
                                    'selected' : '' }}>Yes</option>
                            </select>
                            @if ($errors->has('is_internal'))
                            <small class="text-danger d-block">{{ $errors->first('is_internal') }}</small>
                            @endif
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
                $('#grace_minutes_picker').flatpickr({
                    altInputClass: "form-control",
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    // minTime: "16:00",
                    // maxTime: "22:30",
                })
            })
        </script>
    </x-slot>

</x-app-layout>
