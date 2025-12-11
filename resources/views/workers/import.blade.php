<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>
    <x-flash-message />
    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <!--begin::Card-->
            <form id="import_worker" class="card" enctype="multipart/form-data" method="POST" action="{{ route('import.workers.submit') }}">
                @csrf
                <!--begin::Card body-->
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-4 mb-3">
                            <label for="file" class="required form-label">Upload Workers File (CSV)</label>
                            <input type="file" name="file" id="file" class="form-control form-control-solid" required>
                            <!--begin::Error Message-->
                            @if ($errors->has('file'))
                            <small class="text-danger d-block">{{ $errors->first('file') }}</small>
                            @endif
                            <!--end::Error Message-->
                        </div>
                    </div>

                </div>
                <!--end::Card body-->
                <!--begin::Card footer-->
                <div class="card-footer">
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
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
                $('.input-group-flatpicker').flatpickr({
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'Y-m-d',
                    altInputClass: "form-control"
                })
            })
        </script>
    </x-slot>

</x-app-layout>
