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
            <form id="create_production_product" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.production-product.store')}}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">

                    <div class="row gy-5">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('name') ? 'is-invalid' : '' }}" name="name" id="name" value="{{ old('name') }}" placeholder="" />
                                <label for="name" class="required form-label">Name</label>
                            </div>
                            <small class="text-danger d-block">{{ $errors->first('name') ?? '' }}</small>
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
