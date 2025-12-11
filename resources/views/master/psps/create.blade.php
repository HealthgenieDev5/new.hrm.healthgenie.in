<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Card-->
            <form id="create_company" class="card" enctype="multipart/form-data" method="POST"
                action="{{ route('master.psps.store') }}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control form-control-solid" placeholder="Psp Name" />
                            <!--begin::Error Message-->
                            @if ($errors->has('name'))
                                <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="row">
                                <div class="col-lg-6">
                                    <label class="required form-label">Code</label>
                                    <input type="text" name="code" value="{{ old('code') }}"
                                        class="form-control form-control-solid" placeholder="Psp code" />
                                    <!--begin::Error Message-->
                                    @if ($errors->has('code'))
                                        <small class="text-danger d-block">{{ $errors->first('code') }}</small>
                                    @endif
                                    <!--end::Error Message-->
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div id="logo_select" class="image-input image-input-outline image-input-empty"
                                        data-kt-image-input="true"
                                        style="background-image: url(<?= asset('media/avatars/blank.png') ?>)">
                                        <div class="image-input-wrapper w-70px h-70px">
                                            <a href="#" class="d-none w-100 h-100 overlay preview-button"
                                                data-bs-target="#logo_lightbox" data-bs-toggle="modal">
                                                <div class="overlay-layer card-rounded bg-dark bg-opacity-25 shadow"><i
                                                        class="bi bi-eye-fill text-white fs-2x"></i></div>
                                            </a>
                                        </div>
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Change attachment">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <input type="file" id="logo" name="logo"
                                                accept=".png, .jpg, .jpeg, .pdf" />
                                            <input type="hidden" name="logo_remove" />
                                        </label>
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Cancel attachment">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <span
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip" title=""
                                            data-bs-original-title="Remove attachment">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                        <div class="modal fade" id="logo_lightbox" tabindex="-1"
                                            data-bs-backdrop="static" aria-hidden="true">
                                            <div
                                                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="btn-close" data-bs-toggle="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="min-height: 70vh;">
                                                        <iframe id="logo_lightbox_iframe" class="loaded_content"
                                                            width="100%" height="100%" src=""></iframe>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($errors->has('logo'))
                                        <small class="text-danger d-block">{{ $errors->first('logo') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="form-label">User Account</label>
                            <select name="user_id" class="form-select form-select-solid" data-control="select2"
                                data-placeholder="Select User Account">
                                <option></option>
                                @foreach ($userAccounts as $userAccount)
                                    <option value="{{ $userAccount->id }}"
                                        {{ old('user_id') == $userAccount->id ? 'selected' : '' }}>
                                        {{ $userAccount->name }}<br>
                                        {{ $userAccount->email }}
                                    </option>
                                @endforeach
                            </select>
                            <!--begin::Error Message-->
                            @if ($errors->has('user_id'))
                                <small class="text-danger d-block">{{ $errors->first('user_id') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>



                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">City</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                class="form-control form-control-solid" placeholder="City" />
                            <!--begin::Error Message-->
                            @if ($errors->has('city'))
                                <small class="text-danger d-block">{{ $errors->first('city') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">State</label>
                            <input type="text" name="state" value="{{ old('state') }}"
                                class="form-control form-control-solid" placeholder="State" />
                            <!--begin::Error Message-->
                            @if ($errors->has('state'))
                                <small class="text-danger d-block">{{ $errors->first('state') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Pincode</label>
                            <input type="text" name="pincode" value="{{ old('pincode') }}"
                                class="form-control form-control-solid" placeholder="Pincode" />
                            <!--begin::Error Message-->
                            @if ($errors->has('pincode'))
                                <small class="text-danger d-block">{{ $errors->first('pincode') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Address</label>
                            <textarea class="form-control form-control-solid" name="address" placeholder="Enter address">{{ old('address') }}</textarea>
                            <!--begin::Error Message-->
                            @if ($errors->has('address'))
                                <small class="text-danger d-block">{{ $errors->first('address') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Contact person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                class="form-control form-control-solid" placeholder="Contact person" />
                            <!--begin::Error Message-->
                            @if ($errors->has('contact_person'))
                                <small class="text-danger d-block">{{ $errors->first('contact_person') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Mobile number</label>
                            <input type="text" name="mobile_number" value="{{ old('mobile_number') }}"
                                class="form-control form-control-solid" placeholder="Mobile number" />
                            <!--begin::Error Message-->
                            @if ($errors->has('mobile_number'))
                                <small class="text-danger d-block">{{ $errors->first('mobile_number') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Alternate number</label>
                            <input type="text" name="alternate_number" value="{{ old('alternate_number') }}"
                                class="form-control form-control-solid" placeholder="Alternate number" />
                            <!--begin::Error Message-->
                            @if ($errors->has('alternate_number'))
                                <small class="text-danger d-block">{{ $errors->first('alternate_number') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Email id</label>
                            <input type="text" name="email_id" value="{{ old('email_id') }}"
                                class="form-control form-control-solid" placeholder="Email id" />
                            @if ($errors->has('email_id'))
                                <small class="text-danger d-block">{{ $errors->first('email_id') }}</small>
                            @endif
                        </div>



                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Day Worker target</label>
                            <input type="text" name="day_worker_target" value="{{ old('day_worker_target') }}"
                                class="form-control form-control-solid" placeholder="00" />
                            @if ($errors->has('day_worker_target'))
                                <small class="text-danger d-block">{{ $errors->first('day_worker_target') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Night Worker target</label>
                            <input type="text" name="night_worker_target"
                                value="{{ old('night_worker_target') }}" class="form-control form-control-solid"
                                placeholder="00" />
                            @if ($errors->has('night_worker_target'))
                                <small class="text-danger d-block">{{ $errors->first('night_worker_target') }}</small>
                            @endif
                        </div>


                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
                <!--end::Card footer-->
            </form>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>
