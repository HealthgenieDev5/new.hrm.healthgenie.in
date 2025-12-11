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
                <!--begin::Card body-->
                <div class="card-body table-responsive py-0">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Email
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Roles
                                </th>
                                <th class="text-dark fw-bolder text-hover-primar fs-6">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($users) -->
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('app-settings.users.edit', $user->id)}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-company" data-id="1">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
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
                    {{ $users->links() ?? '' }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>