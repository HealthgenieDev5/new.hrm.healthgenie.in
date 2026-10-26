<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <form id="filter_form" class="card mb-3" method="GET" action="{{route('overrides.manualpunching.index')}}">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="worker_id" id="worker_id" class="form-select form-select-sm" data-control="select2"
                                    data-placeholder="Select Worker" data-allow-clear="true">
                                    <option></option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                        {{ $worker->name }} ({{ $worker->code }})
                                    </option>
                                    @endforeach
                                </select>
                                <label for="worker_id" class="form-label">Workers</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <button type="submit" id="filter_form_submit" class="btn btn-primary btn-sm">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!--begin::Card-->
            <div class="card " style="background-size: auto calc(100% + 10rem); background-position-x: 100%;">
                <!--begin::Card header-->
                <div class="card-header py-3">
                    <div class="card-title">
                    </div>
                    <div class="card-toolbar">
                        <a class="btn btn-sm btn-light-primary" href="{{route('overrides.manualpunching.create')}}">
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
                                    Worker
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Shift Type
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Date
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Punch In
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Punch Out
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Remarks
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($manualPunchings as $manualPunching)
                            <tr>
                                <td>
                                    {{$manualPunching->worker->name}} ({{ $manualPunching->worker->code }})
                                </td>
                                <td class="text-muted fw-bold">
                                    {{$manualPunching->shift_type}}
                                </td>
                                <td class="">
                                    {{\Carbon\Carbon::parse($manualPunching->date)->format('d M Y')}}
                                </td>
                                <td class="">
                                    {{$manualPunching->intime ? \Carbon\Carbon::parse($manualPunching->intime)->format('h:i A') : ''}}
                                </td>
                                <td class="">
                                    {{$manualPunching->outtime ? \Carbon\Carbon::parse($manualPunching->outtime)->format('h:i A') : ''}}
                                </td>
                                <td class="">
                                    <p style="max-width:250px; font-size: 0.75rem">
                                        {{$manualPunching->remarks}}
                                    </p>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('overrides.manualpunching.edit', $manualPunching->id)}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company" data-id="1">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        <form action="{{ route('overrides.manualpunching.destroy', $manualPunching->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" onclick="return confirm('Are you sure?')">
                                                <span class="svg-icon svg-icon-3">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center" colspan="5">
                                    Nothing Found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer py-3">
                    {{ $manualPunchings->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>