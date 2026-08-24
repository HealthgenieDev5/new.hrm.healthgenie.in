<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container-xxl">

            <form id="filter_form" class="card mb-3" method="GET" action="{{ route('overrides.shiftoverride.index') }}">
                <div class="card-body">
                    <div class="row">

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <select name="worker_id" id="worker_id" class="form-select form-select-sm"
                                    data-control="select2" data-placeholder="Select Worker" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($workers as $worker)
                                        <option value="{{ $worker->id }}"
                                            {{ request('worker_id') == $worker->id ? 'selected' : '' }}>
                                            {{ $worker->name }} ({{ $worker->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <label for="worker_id" class="form-label">Workers</label>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <div class="form-floating">
                                <button type="submit" id="filter_form_submit"
                                    class="btn btn-primary btn-sm">Filter</button>
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
                    <div class="card-toolbar d-flex align-items-center justify-content-center gap-3">
                        <a class="btn btn-sm btn-light-primary" href="{{ route('overrides.shiftoverride.create') }}">
                            <i class="fa fa-plus"></i> Add New
                        </a>

                        <a class="btn btn-sm btn-light-primary"
                            href="{{ route('overrides.shiftoverride.createMultiple') }}">
                            <i class="fa fa-plus"></i> Add Multiple
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
                                    Shift
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Start From
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Ends On
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Created at
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Updated at
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shiftoverrides as $shiftoverride)
                                <tr>
                                    <td>
                                        {{ $shiftoverride->worker->name }} ({{ $shiftoverride->worker->code }})
                                    </td>
                                    <td class="text-muted fw-bold">
                                        {{ $shiftoverride->shift->name }}
                                    </td>
                                    <td class="">
                                        {{ \Carbon\Carbon::parse($shiftoverride->start_date)->format('d M Y') }}
                                    </td>
                                    <td class="">
                                        {{ \Carbon\Carbon::parse($shiftoverride->end_date)->format('d M Y') }}
                                    </td>
                                    <td class="">
                                        {{ \Carbon\Carbon::parse($shiftoverride->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="">
                                        {{ \Carbon\Carbon::parse($shiftoverride->updated_at)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('overrides.shiftoverride.edit', $shiftoverride->id) }}"
                                                class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company"
                                                data-id="1">
                                                <span class="svg-icon svg-icon-3">
                                                    <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                                </span>
                                            </a>
                                            <form
                                                action="{{ route('overrides.shiftoverride.destroy', $shiftoverride->id) }}"
                                                method="POST" style="display:inline-block;">
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
                    {{ $shiftoverrides->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>
