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
            <form id="create_holiday" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.holidays.store')}}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Holiday Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-solid" placeholder="Name" />
                            @if ($errors->has('name'))
                            <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label">Date</label>
                            <div class="input-group input-group-solid input-group-flatpicker" id="date_picker" data-wrap="true">
                                <input type="text" id="date" class="form-control" name="date" placeholder="Date" value="{{ old('date') }}" data-input data-open>
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

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Code</label>
                            <select name="code" class="form-select form-select-solid" data-control="select2" data-placeholder="Select Code">
                                <option></option>
                                <option value="HL" {{ old('code') == 'HL' ? 'selected' : '' }}>HL</option>
                                <option value="NH" {{ old('code') == 'NH' ? 'selected' : '' }}>NH</option>
                                <option value="RH" {{ old('code') == 'RH' ? 'selected' : '' }}>RH</option>
                                <option value="SPL HL" {{ old('code') == 'SPL HL' ? 'selected' : '' }}>SPL HL</option>
                            </select>
                            <!--begin::Error Message-->
                            @if ($errors->has('code'))
                            <small class="text-danger d-block">{{ $errors->first('code') }}</small>
                            @endif
                            <!--end::Error Message-->
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
            })
        </script>
    </x-slot>

</x-app-layout>