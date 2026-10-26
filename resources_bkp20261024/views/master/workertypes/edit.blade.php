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
            <form id="edit_workertype" class="card" enctype="multipart/form-data" method="POST"
                action="{{ route('master.workertypes.update', $workertype->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">

                        <div class="col-lg-4 mb-3">
                            <div class="form-floating">
                                <input type="text" name="name" value="{{ old('name', $workertype->name) }}"
                                    class="form-control {{$errors->has('name') ? 'is-invalid' : '' }}" placeholder="Name" />
                                <label class="required form-label">Workertype Name</label>
                            </div>
                            @if ($errors->has('name'))
                            <small class="text-danger d-block">{{ $errors->first('name') }}</small>
                            @endif
                        </div>

                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control {{$errors->has('minimum_wage') ? 'is-invalid' : '' }}" name="minimum_wage" id="minimum_wage" value="{{ old('minimum_wage', $workertype->minimum_wage) }}" placeholder="Minimum Wages" />
                                <label for="minimum_wage" class="required form-label">Minimum Wages</label>
                            </div>
                            @if ($errors->has('minimum_wage'))
                            <small class="text-danger d-block">{{ $errors->first('minimum_wage') ?? '' }}</small>
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
