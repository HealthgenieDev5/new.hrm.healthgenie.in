<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>



    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <!--begin::Container-->
        <div id="kt_content_container" class="container">

            <div class="mx-auto" style="max-width:768px">
                <x-flash-message />
                <div class="container">
                    <form id="recalculate_form" class="mx-auto card mb-3" style="max-width:768px" method="GET" action="{{route('attendance.refresh')}}">
                        <div class="card-body">
                            <div class="d-flex flex-column gap-5">
                                @if (!$user->hasRole('PSP'))
                                <div class="">
                                    <div class="form-floating">
                                        <select id="psp" class="form-select form-select-sm" data-control="select2"
                                            data-placeholder="Select PSP" data-allow-clear="true">
                                            <option value="all" {{ request('psp')=='all' ? 'selected' : '' }}>All PSP</option>
                                            @foreach ($psps as $psp)
                                            <option value="{{$psp->id}}" <?= @$_REQUEST['psp'] == $psp->id ? 'selected' : ''
                                                                            ?>>{{$psp->name}}</option>
                                            @endforeach
                                        </select>
                                        <label for="psp" class="form-label">PSP</label>
                                    </div>
                                </div>
                                @endif

                                <div class="">
                                    <div class="form-floating">
                                        <select name="worker" id="worker" class="form-select form-select-sm"
                                            data-control="select2" data-placeholder="Select worker" data-allow-clear="true">
                                            <option value="" {{ !request('worker') ? 'selected' : '' }}>Select Worker</option>
                                            @foreach ($workers as $worker)
                                            <option value="{{ $worker->code }}" {{ in_array($worker->code, (array)
                                        request('worker')) ? 'selected' : '' }}>
                                                {{ $worker->name }} ({{$worker->code}})
                                            </option>
                                            @endforeach
                                        </select>
                                        <label for=" worker" class="form-label">Worker</label>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="form-floating-flatpickr">
                                        <div class="input-group input-group-flatpicker" id="yearmonth_picker" data-wrap="true">
                                            <input type="text" id="yearmonth" class="form-control form-control-sm"
                                                name="yearmonth"
                                                value="{{ request('yearmonth') ?? $yearMonth }}"
                                                placeholder="joining Date" data-input data-open>
                                            <span class="input-group-text cursor-pointer" data-toggle>
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                        <label class="required form-label">Month</label>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="form-floating">
                                        <button type="submit" id="recalculate_form_submit"
                                            class="btn btn-primary btn-sm">Recalculate</button>
                                    </div>
                                </div>


                            </div>


                        </div>
                    </form>

                </div>
            </div>

        </div>
        <!--end::Container-->
    </div>
    <!--end::Post-->

    <x-slot name="scripts">
        <script src="{{ asset('js/autoscroll-datatable.js') }}"></script>

        <script>
            $(document).ready(function() {
                $('#yearmonth_picker').flatpickr({
                    altInput: true,
                    plugins: [
                        new monthSelectPlugin({
                            shorthand: true,
                            dateFormat: "Y-m",
                            altFormat: "M Y",
                            altInputClass: "form-control form-control-sm",
                            theme: "dark",
                        })
                    ]
                });

                // setTimeout(() => {
                // getFilteredWorkers();
                // }, 300);

                $(document).on('change', 'form#recalculate_form select#psp', function() {
                    getFilteredWorkers();
                });

                function getFilteredWorkers() {
                    let selectedWorkerId = new URLSearchParams(window.location.search).get("workers");
                    $.ajax({
                        url: "{{ route('attendance.get-filtered-workers') }}",
                        type: "GET",
                        data: {
                            psp: $("form#recalculate_form select#psp").val()
                        },
                        success: function(response) {
                            let workersDropdown = $('form#recalculate_form select#workers');
                            workersDropdown.empty();
                            workersDropdown.append('<option value="all">All Workers</option>');

                            $.each(response, function(index, worker) {
                                let selected = (worker.id == selectedWorkerId) ? "selected" : "";
                                workersDropdown.append('<option value="' + worker.id + '" ' + selected + '>' + worker.name + ' (' + worker.code + ')</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.log("Error fetching workers: ", error);
                        }
                    });
                }
            });
        </script>
    </x-slot>

</x-app-layout>