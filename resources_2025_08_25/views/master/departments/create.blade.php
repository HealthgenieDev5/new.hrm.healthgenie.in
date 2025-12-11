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
            <form id="create_department" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.departments.store')}}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Department Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-solid" placeholder="Name" />
                            @if ($errors->has('name'))
                            <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Description</label>
                            <textarea class="form-control form-control-solid" name="description" placeholder="Enter description">{{ old('description') }}</textarea>
                            <!--begin::Error Message-->
                            @if ($errors->has('description'))
                            <small class="text-danger d-block">{{ $errors->first('description') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>

                        <div class="col-lg-12 mb-3">
                            <label class="required form-label">Assign Sub Dept</label>
                            <div class="d-flex align-items-center gap-5 flex-wrap">
                                @foreach($subdepartments as $subdepartment)

                                <div class="form-check form-check-sm form-check-custom form-check-solid border rounded p-2 pe-4 bg-secondary">
                                    <input class="form-check-input" type="checkbox" type="checkbox" name="subdepartments[]" value="{{ $subdepartment->id }}" id="subdepartment_{{$subdepartment->id}}" />
                                    <label class="form-check-label" for="subdepartment_{{$subdepartment->id}}">
                                        {{ $subdepartment->name }}
                                    </label>
                                </div>

                                @endforeach
                            </div>

                            @if ($errors->has('subdepartments'))
                            <small class="text-danger d-block">{{ $errors->first('subdepartments') }}</small>
                            @endif
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