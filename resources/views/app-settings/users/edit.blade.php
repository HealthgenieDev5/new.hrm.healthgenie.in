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
            <form id="edit_user" class="card" enctype="multipart/form-data" method="POST" action="{{ route('app-settings.users.update', $user->id)}}">
                @csrf
                @method('PUT')
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6 mb-3">
                            <label class="required form-label">Roles</label>
                            <div class="d-flex align-items-center gap-5 flex-wrap">
                                @foreach($roles as $role)
                                <div class="form-check form-check-sm form-check-custom form-check-solid border rounded p-2 pe-4 bg-secondary">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{$role->id}}"
                                        {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="role_{{$role->id}}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>

                            @if ($errors->has('role'))
                            <small class="text-danger d-block">{{ $errors->first('role') }}</small>
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