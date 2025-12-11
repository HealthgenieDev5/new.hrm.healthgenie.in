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
                        <a class="btn btn-sm btn-light-primary" href="{{route('master.production-product.create')}}">
                            <i class="fa fa-plus"></i> Add New
                        </a>
                    </div>
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body table-responsive py-0">
                    <style>
                        table.nowrap tr th,
                        table.nowrap tr td {
                            white-space: nowrap;
                        }
                    </style>
                    <table class="table table-row-dashed table-row-gray-300 align-middle gy-4 nowrap text-center">
                        <thead>
                            <tr>
                                <th class="text-dark fw-bolder text-hover-primary fs-6">
                                    Name
                                </th>
                                <th class="text-dark fw-bolder text-hover-primary fs-6 text-center">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- @if ($productionProducts) -->
                            @foreach ($productionProducts as $productionProduct)
                            <tr>
                                <td>
                                    {{$productionProduct->name}}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        @hasPermissionTo('Edit Production Product')
                                        <a href="{{ route('master.production-product.edit', $productionProduct->id)}}"
                                            class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit-production-product"
                                            data-id="1">
                                            <span class="svg-icon svg-icon-3">
                                                <i class="fa fa-pencil-alt" aria-hidden="true"></i>
                                            </span>
                                        </a>
                                        @endhasPermissionTo
                                        @hasPermissionTo('Delete Production Product')
                                        <form action="{{ route('master.production-product.destroy', $productionProduct->id) }}"
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
                    {{ $productionProducts->links() }}
                </div>
                <!--end::Card footer-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

</x-app-layout>
