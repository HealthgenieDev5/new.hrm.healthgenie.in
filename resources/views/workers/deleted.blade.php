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
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body table-responsive py-0">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Code
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Deleted By
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Deleted At
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($deletedWorkers) -->
                            @foreach ($deletedWorkers as $worker)
                            <tr>
                                <td>{{ $worker->code }}</td>
                                <td>{{ $worker->name }}</td>
                                <td>{{ $worker->deleter->name ?? 'System' }}</td>
                                <td>{{ $worker->deleted_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        @hasPermissionTo('Restore Deleted')
                                        <form action="{{ route('workers.restore', $worker->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company">Restore</button>
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
                    {{ $deletedWorkers->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>