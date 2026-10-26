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
            <form id="edit_division" class="card" enctype="multipart/form-data" method="POST" action="{{ route('master.divisions.update', $division->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Division Name</label>
                            <input type="text" name="name" value="{{ old('name', $division->name) }}" class="form-control form-control-solid" placeholder="Name" />
                            @if ($errors->has('name'))
                            <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Assign Departments</label>
                            <div class="d-flex align-items-center gap-5 flex-wrap">
                                @foreach($departments as $department)
                                <div class="form-check form-check-sm form-check-custom form-check-solid border rounded p-2 pe-4 bg-secondary">
                                    <input class="form-check-input" type="checkbox" name="departments[]" value="{{ $department->id }}" id="department_{{$department->id}}"
                                        {{ $division->departments->contains('id', $department->id) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="department_{{$department->id}}">
                                        {{ $department->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>

                            @if ($errors->has('departments'))
                            <small class="text-danger d-block">{{ $errors->first('departments') }}</small>
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
                        <span class="menu-title">Update</span>
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