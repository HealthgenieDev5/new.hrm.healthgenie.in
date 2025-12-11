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
                    </div>
                    <div class="card-toolbar">
                        <a class="btn btn-sm btn-light-primary" href="{{route('master.shifts.create')}}">
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
                                    ID
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Grace
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Week Off
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Sub Type
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Sister Shift
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Internal
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($shifts) -->
                            @foreach ($shifts as $shift)
                            <tr>
                                <td>
                                    {{$shift->id}}
                                </td>
                                <td>
                                    {{$shift->name}}
                                </td>
                                <td>
                                    {{$shift->grace_minutes}}
                                </td>
                                <td>
                                    {{$shift->week_off}}
                                </td>
                                <td class="text-capitalize">
                                    {{$shift->sub_type}}
                                    <br>
                                    <p style="max-width: 200px; font-size: 12px;">
                                        <small style="color: var(--bs-orange)">
                                        {{$shift->sub_type == 'reduced' ? 'Cleaning time reduced, so it will be 30 minutes less than its sister shift' : ''}}
                                    </small>
                                    </p>
                                </td>
                                <td class="text-capitalize">
                                    {{$shift?->sisterShift?->name}}
                                </td>
                                <td class="text-capitalize">
                                    {{$shift?->is_internal ? 'Yes' : 'No'}}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        @hasPermissionTo('Edit shift')
                                        <a href="{{ route('master.shifts.edit', $shift->id)}}"
                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company"
                                            data-id="1">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        @endhasPermissionTo
                                        @hasPermissionTo('Delete shift')
                                        <form action="{{ route('master.shifts.destroy', $shift->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                                onclick="return confirm('Are you sure?')">
                                                <span class="svg-icon svg-icon-3">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </span>
                                            </button>
                                        </form>
                                        @endhasPermissionTo
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
                    {{ $shifts->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>
