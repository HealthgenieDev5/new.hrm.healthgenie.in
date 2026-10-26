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
                <div class="card-header py-3 gap-5">
                    <!-- <div class="card-title"> -->
                    <div class="card-toolbar flex-grow-1">
                        <form id="filter_form" class="w-100" method="GET" action="{{route('master.departments.index')}}">
                            <div class="row gy-5">
                                <div class="col-lg-2 col-md-3">
                                    <div class="form-floating">
                                        <select name="division_id" id="division_id" class="form-select form-select-sm" data-control="select2"
                                            data-placeholder="Select division" data-allow-clear="true">
                                            <option value="all" {{ request('division_id')=='all' ? 'selected' : '' }}>All division</option>
                                            @foreach ($divisions as $division)
                                            <option value="{{$division->id}}" <?= @$_REQUEST['division_id'] == $division->id ? 'selected' : '' ?>>
                                                {{$division->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                        <label for="division_id" class="form-label">Division</label>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-3">
                                    <div class="form-floating">
                                        <button type="submit" id="filter_form_submit"
                                            class="btn btn-primary btn-sm">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-toolbar">
                        <a class="btn btn-sm btn-light-primary" href="{{route('master.departments.create')}}">
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
                                <th class="text-dark fw-bolder text-hover-primar fs-6" style="min-width: 350px;">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Description
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    No Of Sub dept
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Sub depts
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6" style="min-width: 100px;">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($departments) -->
                            @foreach ($departments as $department)
                            <tr>
                                <td>
                                    {{$department->id}}
                                </td>
                                <td>
                                    {{$department->name}}
                                </td>
                                <td class="text-muted fw-bold">
                                    <small>
                                        {{$department->description}}
                                    </small>
                                </td>
                                <td class="text-muted fw-bold">
                                    {{$department->subdepartmentscount}}
                                </td>
                                <td class="">
                                    <div class="d-flex align-items-center flex-wrap gap-3" style="max-width: 300px;">
                                        @foreach($department->subdepartments as $subdepartment)
                                        <span class="badge bg-primary">{{ $subdepartment->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        @hasPermissionTo('Edit department')
                                        <a href="{{ route('master.departments.edit', $department->id)}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company" data-id="1">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        @endhasPermissionTo
                                        @hasPermissionTo('Delete department')
                                        <form action="{{ route('master.departments.destroy', $department->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="return confirm('Are you sure?')">
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
                    {{ $departments->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>