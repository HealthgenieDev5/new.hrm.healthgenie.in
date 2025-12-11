<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-flash-message />

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div class="container">

            <!--begin::Card-->
            <div class="card mx-auto" style="max-width: 600px">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 order-2 order-lg-1">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>PSP:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->psp?->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Name:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Code:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->code }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Father's name:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->father_name }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Gender:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->gender }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Shift:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->shift?->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Mobile no:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->mobile_number }}</strong>
                                </li>
                                <li class="list-group-item d-flex align-items-center gap-3 border-0 flex-wrap">
                                    <span>Adhar no:</span>
                                    <strong class="fs-4 text-capitalize">{{ $worker?->adhar_number }}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-4">
                            <div class="symbol overflow-hidden" style="max-width: 150px; width: 100%; height: auto;">
                                <img src="{{ $worker->photo ? Storage::url($worker->photo) : asset('media/avatars/blank.png') }}" style="object-fit: contain; max-width: 100%; width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
            <p class="text-center mt-3"><small class="text-muted">Take screenshot of this page and share with the worker</small></p>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->


    <x-slot name="scripts">
        <script>

        </script>
    </x-slot>

</x-app-layout>
