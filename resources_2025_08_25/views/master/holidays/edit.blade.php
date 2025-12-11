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

                        <div class="col-lg-8 col-md-6 mb-3">
                            <div class="row gy-5">
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Assign to Workers</label>
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <div class="mb-3">
                                        <label for="division" class="form-label">Filter by Division</label>
                                        <div id="division-list" class="d-flex gap-3 flex-wrap">
                                            @foreach($divisions as $division)
                                            <div class="form-check form-check-sm form-check-custom form-check-solid border rounded p-2 pe-4 bg-secondary">
                                                <input class="form-check-input" type="checkbox" value="{{$division->id}}" id="division_{{$division->id}}">
                                                <label class="form-check-label" for="division_{{$division->id}}">{{$division->name}}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="worker-list" class="form-label">Assign Workers</label>
                                        <div id="worker-list" class="d-flex gap-3 flex-wrap">

                                            @foreach($workers as $worker)
                                            <div class="form-check" data-division_id="{{$worker->division->id ?? '0'}}">
                                                <input class="form-check-input" type="checkbox" name="workers[]" value="{{$worker->id}}" id="worker_{{$worker->id}}" {{$worker->holidays->contains('id', $holiday->id) ? 'checked' : ''}}>
                                                <label class="form-check-label" for="worker_{{$worker->id}}">{{$worker->name}}</label>
                                            </div>
                                            @endforeach

                                        </div>
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
                $('.input-group-flatpicker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: "form-control"
                })

                $(document).on('change', '#division-list input', function() {
                    let $selected_division = $(this).val();

                    if ($(this).prop('checked')) {
                        console.log("selected_division", $selected_division);
                        console.log("Current", $(this).prop('checked'));

                        // Select all workers belonging to the selected division
                        $('#worker-list .form-check[data-division_id="' + $selected_division + '"] input')
                            .prop('checked', true)
                            .trigger('change');
                    } else {
                        console.log("selected_division", $selected_division);
                        console.log("Current", $(this).prop('checked'));

                        // Deselect all workers belonging to the unselected division
                        $('#worker-list .form-check[data-division_id="' + $selected_division + '"] input')
                            .prop('checked', false)
                            .trigger('change');
                    }
                });

            })
        </script>


    </x-slot>

</x-app-layout>