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
            <form id="create_salary_master" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.salary-master.store')}}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <select name="worker_id" id="worker_id" class="form-select {{$errors->has('worker_id') ? 'is-invalid' : '' }}" data-control="select2" data-placeholder="Select Worker" data-allow-clear="true">
                                    <option value="">All worker</option>
                                    @foreach ($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ (string) old('worker_id', request('worker')) == (string) $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }} ({{$worker->code}})
                                    </option>
                                    @endforeach
                                </select>
                                <label for="worker_id" class="form-label">Worker</label>
                            </div>
                            @if ($errors->has('worker_id'))
                            <small class="text-danger d-block">{{ $errors->first('worker_id') }}</small>
                            @endif
                        </div>




                        {{-- Basic Salary --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('basic') ? 'is-invalid' : '' }}" name="basic" id="basic" value="{{ old('basic') }}" placeholder="" />
                                <label for="basic" class="required form-label">Basic Salary</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('basic') ?? '' }}</small>
                        </div>

                        {{-- HRA --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('hra') ? 'is-invalid' : '' }}" name="hra" id="hra" value="{{ old('hra') }}" placeholder="" />
                                <label for="hra" class="required form-label">HRA</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('hra') ?? '' }}</small>
                        </div>

                        {{-- Conveyance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('conveyance') ? 'is-invalid' : '' }}" name="conveyance" id="conveyance" value="{{ old('conveyance') }}" placeholder="" />
                                <label for="conveyance" class="required form-label">Conveyance Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('conveyance') ?? '' }}</small>
                        </div>

                        {{-- Medical Allowance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('medical_allowance') ? 'is-invalid' : '' }}" name="medical_allowance" id="medical_allowance" value="{{ old('medical_allowance') }}" placeholder="" />
                                <label for="medical_allowance" class="required form-label">Medical Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('medical_allowance') ?? '' }}</small>
                        </div>

                        {{-- Special Allowance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('special_allowance') ? 'is-invalid' : '' }}" name="special_allowance" id="special_allowance" value="{{ old('special_allowance') }}" placeholder="" />
                                <label for="special_allowance" class="required form-label">Special Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('special_allowance') ?? '' }}</small>
                        </div>

                        {{-- Fuel Allowance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('fuel_allowance') ? 'is-invalid' : '' }}" name="fuel_allowance" id="fuel_allowance" value="{{ old('fuel_allowance') }}" placeholder="" />
                                <label for="fuel_allowance" class="required form-label">Fuel Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('fuel_allowance') ?? '' }}</small>
                        </div>

                        {{-- Vacation Allowance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('vacation_allowance') ? 'is-invalid' : '' }}" name="vacation_allowance" id="vacation_allowance" value="{{ old('vacation_allowance') }}" placeholder="" />
                                <label for="vacation_allowance" class="required form-label">Vacation Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('vacation_allowance') ?? '' }}</small>
                        </div>

                        {{-- Other Allowance --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('other_allowance') ? 'is-invalid' : '' }}" name="other_allowance" id="other_allowance" value="{{ old('other_allowance') }}" placeholder="" />
                                <label for="other_allowance" class="required form-label">Other Allowance</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('other_allowance') ?? '' }}</small>
                        </div>

                        {{-- Bonus Allowed --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <select name="bonus_allowed" id="bonus_allowed" class="form-select {{$errors->has('bonus_allowed') ? 'is-invalid' : '' }}" data-control="select2">
                                    <option value="0" {{ old('bonus_allowed') == "0" ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('bonus_allowed') == "1" ? 'selected' : '' }}>Yes</option>
                                </select>
                                <label for="bonus_allowed" class="required form-label">Bonus Allowed</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('bonus_allowed') ?? '' }}</small>
                        </div>

                        {{-- PF Allowed --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <select name="pf_allowed" id="pf_allowed" class="form-select {{$errors->has('pf_allowed') ? 'is-invalid' : '' }}" data-control="select2">
                                    <option value="0" {{ old('pf_allowed') == "0" ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('pf_allowed') == "1" ? 'selected' : '' }}>Yes</option>
                                </select>
                                <label for="pf_allowed" class="required form-label">PF Allowed</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('pf_allowed') ?? '' }}</small>
                        </div>

                        {{-- ESI Allowed --}}
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <select name="esic_allowed" id="esic_allowed" class="form-select {{$errors->has('esic_allowed') ? 'is-invalid' : '' }}" data-control="select2">
                                    <option value="0" {{ old('esic_allowed') == "0" ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ old('esic_allowed') == "1" ? 'selected' : '' }}>Yes</option>
                                </select>
                                <label for="esic_allowed" class="required form-label">ESIC Allowed</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('esic_allowed') ?? '' }}</small>
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

            })
        </script>
    </x-slot>

</x-app-layout>