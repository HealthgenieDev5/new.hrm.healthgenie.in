<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <style>
                .form-floating .select2-selection {
                    height: calc(2.5rem + 2px) !important;
                }
            </style>

            <form id="pdf_form" class="card mb-3" method="GET" action="{{route('reports.attendance-report.generate-pdf')}}">
                <div class="card-body">
                    <div class="row">

                        @if (!$user->hasRole('PSP'))
                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="psp" id="psp" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select PSP" data-allow-clear="true">
                                    <option value="all" {{ request('psp') == 'all' ? 'selected' : '' }}>All PSP</option>
                                    @foreach ($psps as $psp)
                                    <option value="{{$psp->id}}" <?= @$_REQUEST['psp'] == $psp->id ? 'selected' : '' ?>>{{$psp->name}}</option>
                                    @endforeach
                                </select>
                                <label for="psp" class="form-label">PSP</label>
                            </div>
                        </div>
                        @endif


                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="shift" id="shift" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select Shift" data-allow-clear="true">
                                    <option value="all" {{ request('shift') == 'all' ? 'selected' : '' }}>All Shifts</option>
                                    @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ request('shift') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <label for="psp" class="form-label">Shift</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="employment_status" id="employment_status" class="form-select form-select-sm" data-control="select2" data-placeholder="Select Status" data-allow-clear="true">
                                    <option value="Active" {{ request('employment_status') == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Left" {{ request('employment_status') == 'Left' ? 'selected' : '' }}>Left</option>
                                    <option value="Abscond" {{ request('employment_status') == 'Abscond' ? 'selected' : '' }}>Abscond</option>
                                    <option value="Under Resignation" {{ request('employment_status') == 'Under Resignation' ? 'selected' : '' }}>Under Resignation</option>
                                    <option value="Working" {{ request('employment_status') == 'Working' ? 'selected' : '' }}>Working (Other than Left)</option>
                                </select>
                                <label for="employment_status" class="form-label">Employement Status</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating-flatpickr">
                                <div class="input-group input-group-flatpicker" id="yearmonth_picker" data-wrap="true">
                                    <input type="text" id="yearmonth" class="form-control form-control-sm" name="yearmonth" value="{{ request('yearmonth') ??  \Carbon\Carbon::parse($currentDate)->format('Y-m') }}" placeholder="joining Date" data-input data-open>
                                    <span class="input-group-text cursor-pointer" data-toggle>
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <label class="required form-label">Month</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 d-flex align-items-end">
                            <label for="only_weekoff_working" class="badge badge-warning d-flex align-items-center justify-content-center gap-3 cursor-pointer" style="width: max-content;">
                                <input type="checkbox" name="only_weekoff_working" id="only_weekoff_working" class="cursor-pointer" value="yes" {{ request('only_weekoff_working') == 'yes' ? 'checked' : '' }} />
                                <small class="text-dark">Only Weekoff Working</small>
                            </label>
                        </div>


                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <button type="submit" id="" class="btn btn-primary btn-sm">Get PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#yearmonth_picker').flatpickr({
                    dateFormat: 'Y-m',
                    altInput: true,
                    altFormat: 'M Y',
                    altInputClass: "form-control form-control-sm"
                })
            })
        </script>
    </x-slot>

</x-app-layout>