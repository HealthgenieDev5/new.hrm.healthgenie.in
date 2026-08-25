<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">
            <!--begin::Card-->
            <div class="card " style="background-size: auto calc(100% + 10rem); background-position-x: 100%;">
                <!--begin::Card header-->
                <div class="card-header py-3">
                    <div class="card-title">
                        <!-- <form>
                            <select>
                                <option>All</option>
                                <option>Psp 1</option>
                                <option>Psp 2</option>
                                <option>Psp 3</option>
                            </select>
                        </form> -->
                    </div>
                    <div class="card-toolbar">
                        <a class="btn btn-sm btn-light-primary" href="{{ route('master.psps.create') }}">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body table-responsive py-0">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Logo
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Code
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Address
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Target
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Contact Person
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Status
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($psps) -->
                            @foreach ($psps as $psp)
                                <tr>
                                    <td>
                                        <div class="symbol symbol-45px me-5">
                                            @if ($psp->logo)
                                                <img src="{{ Storage::url($psp->logo) }}" alt="Psp Logo">
                                            @else
                                                <img class="w-full" src="{{ asset('media/brand-logos/chrome.svg') }}"
                                                    alt="user">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6">
                                                {{ $psp->name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bolder text-hover-primary mb-1 fs-6">
                                                {{ $psp->code }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-muted fw-bold">
                                        <small class="">{{ $psp->address }}</small><br>
                                        <small class="">{{ $psp->city }}, {{ $psp->state }},
                                            {{ $psp->pincode }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <small
                                                class="text-dark fw-bolder text-hover-primary d-block fs-6">{{ $psp->contact_person }}</small>
                                            <small>{{ $psp->mobile_number }}</small>
                                            <small>{{ $psp->email_id ?? '' }}</small>
                                            <small>{{ $psp->alternate_number ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class=" gap-1">
                                            <small>{{ $psp->day_worker_target ?? '00' }}</small> /
                                            <small>{{ $psp->night_worker_target ?? '00' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($psp->is_active)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger d-block mb-1">Inactive</span>
                                            @if ($psp->deactivated_at)
                                                <small class="text-muted d-block">
                                                    {{ $psp->deactivated_at->format('d M Y, h:i A') }}
                                                    @if ($psp->deactivatedBy)
                                                        by {{ $psp->deactivatedBy->name }}
                                                    @endif
                                                </small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            @hasPermissionTo('Edit Psp')
                                                <a href="{{ route('master.psps.edit', $psp->id) }}"
                                                    class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company"
                                                    data-id="1">
                                                    <span class="svg-icon svg-icon-3">
                                                        <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                                    </span>
                                                </a>
                                                <form action="{{ route('master.psps.toggle-status', $psp->id) }}"
                                                    method="POST" class="d-inline me-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                                                        title="{{ $psp->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <span class="svg-icon svg-icon-3">
                                                            @if ($psp->is_active)
                                                                <i class="fa fa-toggle-on" aria-hidden="true"></i>
                                                            @else
                                                                <i class="fa fa-toggle-off" aria-hidden="true"></i>
                                                            @endif
                                                        </span>
                                                    </button>
                                                </form>
                                            @endhasPermissionTo
                                            <a href="#"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm delete-company"
                                                data-id="1">
                                                <span class="svg-icon svg-icon-3">
                                                    <i class="fas fa-trash"></i>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- @endif -->
                        </tbody>
                    </table>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer py-3">
                    {{ $psps->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>
