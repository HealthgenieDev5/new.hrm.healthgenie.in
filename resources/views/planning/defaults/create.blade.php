<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <x-slot name="headerComponents">

    </x-slot>

    <x-flash-message />

    <style>
        .draggable-zone .worker-info {
            font-size: 0.5rem;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-top-left-radius: 0;
        }

        .draggable-zone li:first-child .worker-info {
            border-top-right-radius: 0.375rem;
        }

        .draggable-zone .draggable {
            cursor: move;
        }

        .draggable-zone .draggable[data-permanent="1"] {
            cursor: not-allowed;
            opacity: 0.7;
        }

        input[readonly]::-webkit-outer-spin-button,
        input[readonly]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[readonly] {
            -moz-appearance: textfield;
            border: none;
            font-weight: bold;
        }

        .card .badge {
            font-size: 0.95rem;
            line-height: 1.2;
            min-width: 60px;
            min-height: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .offcanvas .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f1f1;
        }

        .offcanvas .list-group-item:last-child {
            border-bottom: none;
        }
    </style>

    <!--begin::Post-->
    <div class="post d-flex flex-column-fluid" id="kt_post">

        <div class="container">
            <div class="row">
                <div class="col-md-4">

                    <form id="create_department" class="card" enctype="multipart/form-data" method="POST"
                        action="{{ route('production.planning.store') }}">
                        @csrf

                        <div class="card-header">
                            <div class="d-flex flex-wrap justify-content-between gap-5 my-3 w-100">
                                <div>
                                    <div class="mb-2">
                                        <strong>Line: </strong> {{ $existingRequirement['line_name'] }}
                                        <input type="hidden" name="line_id"
                                            value="{{ $existingRequirement['line_id'] }}" />
                                    </div>
                                    <div class="mb-0">
                                        <strong>Planning for: </strong> {{ now()->toFormattedDateString() }}
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary" id="edit-button">
                                        <span class="menu-icon">
                                            <i class="fa fa-pencil">
                                            </i>
                                        </span>
                                        <span class="menu-title">Edit</span>
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-success d-none" id="submit-button">
                                        <span class="menu-icon">
                                            <i class="fa fa-check">
                                            </i>
                                        </span>
                                        <span class="menu-title">Save</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row gy-5">
                                <div class="col-lg-6">
                                    <h4 class="mb-5">
                                        Day
                                        <small id="day_total">
                                            (
                                            Total
                                            {{ array_sum(array_values($existingRequirement['day']->all())) }}
                                            )
                                        </small>
                                    </h4>
                                    @include('planning.partial._shift_fields', [
                                    'prefix' => 'day',
                                    'existingRequirement' => $existingRequirement,
                                    ])
                                </div>
                                <div class="col-lg-6">
                                    <h4 class="mb-5">
                                        Night
                                        <small id="night_total">
                                            (
                                            Total
                                            {{ array_sum(array_values($existingRequirement['night']->all())) }}
                                            )
                                        </small>
                                    </h4>
                                    @include('planning.partial._shift_fields', [
                                    'prefix' => 'night',
                                    'existingRequirement' => $existingRequirement,
                                    ])
                                </div>

                            </div>

                        </div>

                    </form>

                </div>
                <div class="col-md-8">
                    @php
                    $lineId = $existingRequirement['line_id'];
                    @endphp
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Currently Assigned (Active)
                            </div>

                            <div class="card-toolbar d-flex gap-2">
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#assignWorkerModal">+ Assign Worker</button>
                                <a class="btn btn-sm btn-success" target="_blank"
                                    href="{{ route('production.planning.export-assigned-workers', ['line_id' => $lineId]) }}">Download
                                    CSV</a>
                                <button id="processOffcanvas_button" class="btn btn-sm btn-secondary shadow">View by
                                    Process</button>
                            </div>
                        </div>
                        <div class="card-body">


                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Day Shift</h4>
                                    <div class="d-flex flex-wrap gap-3 mb-7">
                                        <div class="badge bg-primary p-2">
                                            Present:
                                            <strong>{{ $existingRequirement['presentDayCount'] }}/{{ $existingRequirement['assignedDayWorkers']->count() }}</strong>
                                        </div>
                                    </div>

                                    @php
                                    $dayWorkers = $existingRequirement['assignedDayWorkers']->sortBy(function (
                                    $w,
                                    ) use ($lineId) {
                                    $absent = in_array($w->attendance_status, ['A']);
                                    $present = in_array($w->attendance_status, ['P', 'M/P']);
                                    if ($absent) {
                                    return 0;
                                    }
                                    if ($w->line_id != $lineId) {
                                    return 1;
                                    }
                                    if ($present) {
                                    return 2;
                                    }
                                    return 3;
                                    });
                                    @endphp
                                    <ul class="list-group mb-4 draggable-zone">
                                        @foreach ($dayWorkers as $worker)
                                        @php
                                        $isPermanent = $worker->line_id == $lineId;
                                        $statusBadge = in_array($worker->attendance_status, ['P', 'M/P'])
                                        ? 'Present'
                                        : 'Absent';
                                        @endphp
                                        <li class="list-group-item position-relative d-flex p-0"
                                            data-worker-id="{{ $worker->id }}"
                                            data-permanent="{{ $isPermanent ? '1' : '0' }}"
                                            @if ($isPermanent) style="background-color: #f0fdf4;"
                                            @else style="background-color: #c1dcff" @endif>

                                            <div class="details px-3 py-2 d-flex flex-column gap-1 flex-grow-1">
                                                <div>
                                                    <strong
                                                        class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }}">{{ $worker->name }}</strong>
                                                    <small class="">({{ $worker->code }})</small>
                                                    <small
                                                        class="">-<strong>{{ optional($worker->level)->name ?? '-' }}</strong></small>
                                                </div>

                                                @if ($isPermanent)
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="">{{ $worker?->line?->name }} -
                                                        {{ $worker?->process?->name }}</small>
                                                </div>
                                                @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <small
                                                        class="text-decoration-line-through">{{ $worker?->line?->name }}
                                                        - {{ $worker?->process?->name }}</small>
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                    <small
                                                        class="text-primary">{{ $existingRequirement['line_name'] }}
                                                        -
                                                        {{ $worker->temp_process->name ?? ($worker?->process?->name ?? '-') }}</small>
                                                </div>
                                                @endif

                                            </div>

                                            <div class="d-flex flex-column align-items-stretch justify-content-center h-full"
                                                style="border-left: 1px solid rgba(0, 0, 0, .125); font-size:0.75rem; min-width: 70px;">
                                                <strong
                                                    class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0"
                                                    style="border-bottom: 1px solid rgba(0, 0, 0, 0.0625); margin-bottom:-1px;">
                                                    {{ $statusBadge }}
                                                </strong>
                                                <strong
                                                    class="{{ $isPermanent ? 'text-muted' : 'text-primary' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0 px-2"
                                                    style="border-top: 1px solid rgba(0, 0, 0, 0.0625);">
                                                    {{ $isPermanent ? 'Permanent' : 'Shifted' }}
                                                </strong>
                                            </div>

                                            @if (!$isPermanent)
                                            <i class="fa fa-times remove-worker" data-id="{{ $worker->id }}"
                                                style="cursor: pointer;
                                                        position: absolute;
                                                        right: 0;
                                                        top: 50%;
                                                        transform: translate(50%, -50%);
                                                        z-index: 1;
                                                        background: rgba(0,0,0,0.25);
                                                        width: 15px;
                                                        height: 15px;
                                                        line-height: 15px;
                                                        text-align: center;
                                                        color: white;
                                                        border-radius: 15px;"></i>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h4>Night Shift</h4>
                                    <div class="d-flex flex-wrap gap-3 mb-7">
                                        <div class="badge bg-primary p-2">
                                            Present:
                                            <strong>{{ $existingRequirement['presentNightCount'] }}/{{ $existingRequirement['assignedNightWorkers']->count() }}</strong>
                                        </div>
                                    </div>

                                    @php
                                    $nightWorkers = $existingRequirement['assignedNightWorkers']->sortBy(function (
                                    $w,
                                    ) use ($lineId) {
                                    $absent = in_array($w->attendance_status, ['A']);
                                    $present = in_array($w->attendance_status, ['P', 'M/P']);
                                    if ($absent) {
                                    return 0;
                                    }
                                    if ($w->line_id != $lineId) {
                                    return 1;
                                    }
                                    if ($present) {
                                    return 2;
                                    }
                                    return 3;
                                    });
                                    @endphp
                                    <ul class="list-group mb-4 draggable-zone">
                                        @foreach ($nightWorkers as $worker)
                                        @php
                                        $isPermanent = $worker->line_id == $lineId;
                                        $statusBadge = in_array($worker->attendance_status, ['P', 'M/P'])
                                        ? 'Present'
                                        : 'Absent';
                                        @endphp
                                        <li class="list-group-item position-relative d-flex p-0"
                                            data-worker-id="{{ $worker->id }}"
                                            data-permanent="{{ $isPermanent ? '1' : '0' }}"
                                            @if ($isPermanent) style="background-color: #f0fdf4;"
                                            @else style="background-color: #c1dcff" @endif>

                                            <div class="details px-3 py-2 d-flex flex-column gap-1 flex-grow-1">
                                                <div>
                                                    <strong
                                                        class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }}">{{ $worker->name }}</strong>
                                                    <small class="">({{ $worker->code }})</small>
                                                    <small
                                                        class="">-<strong>{{ optional($worker->level)->name ?? '-' }}</strong></small>
                                                </div>

                                                @if ($isPermanent)
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="">{{ $worker?->line?->name }} -
                                                        {{ $worker?->process?->name }}</small>
                                                </div>
                                                @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <small
                                                        class="text-decoration-line-through">{{ $worker?->line?->name }}
                                                        - {{ $worker?->process?->name }}</small>
                                                    <i class="fa-solid fa-arrow-right"></i>
                                                    <small
                                                        class="text-primary">{{ $existingRequirement['line_name'] }}
                                                        -
                                                        {{ $worker->temp_process->name ?? ($worker?->process?->name ?? '-') }}</small>
                                                </div>
                                                @endif

                                            </div>

                                            <div class="d-flex flex-column align-items-stretch justify-content-center h-full"
                                                style="border-left: 1px solid rgba(0, 0, 0, .125); font-size:0.75rem; min-width: 70px;">
                                                <strong
                                                    class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0"
                                                    style="border-bottom: 1px solid rgba(0, 0, 0, 0.0625); margin-bottom:-1px;">
                                                    {{ $statusBadge }}
                                                </strong>
                                                <strong
                                                    class="{{ $isPermanent ? 'text-muted' : 'text-primary' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0 px-2"
                                                    style="border-top: 1px solid rgba(0, 0, 0, 0.0625);">
                                                    {{ $isPermanent ? 'Permanent' : 'Shifted' }}
                                                </strong>
                                            </div>

                                            @if (!$isPermanent)
                                            <i class="fa fa-times remove-worker"
                                                data-id="{{ $worker->id }}"
                                                style="cursor: pointer;
                                                        position: absolute;
                                                        right: 0;
                                                        top: 50%;
                                                        transform: translate(50%, -50%);
                                                        z-index: 1;
                                                        background: rgba(0,0,0,0.25);
                                                        width: 15px;
                                                        height: 15px;
                                                        line-height: 15px;
                                                        text-align: center;
                                                        color: white;
                                                        border-radius: 15px;"></i>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div class="modal fade" id="assignWorkerModal" tabindex="-1" aria-labelledby="assignWorkerLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form class="addWorkersToLine">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Workers to {{ $existingRequirement['line_name'] }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="line_id" value="{{ $lineId }}">
                        <div class="mb-3">
                            <label>Select Worker(s)</label>
                            <select name="worker_ids[]" id="worker_ids_{{ $lineId }}"
                                class="form-select worker_ids" data-control="select2"
                                data-placeholder="Select Worker" multiple>
                                <option></option>
                                @foreach ($existingRequirement['availableWorkers'] as $worker)
                                <option value="{{ $worker->id }}" data-name="{{ $worker->name }}"
                                    data-code="{{ $worker->code }}"
                                    data-level="{{ optional($worker->level)->name }}"
                                    data-line="{{ optional($worker->line)->name }}"
                                    data-status="{{ in_array($worker->attendance_status, ['P', 'M/P']) ? 'Present' : 'Absent' }}">
                                    {{ $worker->name }} ({{ $worker->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label>Shift</label>
                            <select name="shift_id" class="form-control">
                                @foreach ($existingRequirement['shifts'] as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-3">
                            <label>Process</label>
                            <select name="process_id" id="process_id" class="form-control">
                                <option value="">Select Process</option>
                                @foreach ($existingRequirement['processes'] as $process)
                                <option value="{{ $process->id }}">{{ $process->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div id="processOffcanvas" class="bg-white" data-kt-drawer="true" data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#processOffcanvas_button" data-kt-drawer-close="#processOffcanvas_close"
        data-kt-drawer-name="processOffcanvas" data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'350px', 'md': '768px'}" data-kt-drawer-direction="end">
        <div class="card rounded-0 w-100 border-0">
            <div class="card-header pe-5">
                <div class="card-title">
                    <div class="d-flex justify-content-center flex-column me-3">
                        <span class="fs-4 fw-bolder lh-1">Workers by Process</span>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="btn btn-sm btn-icon btn-active-light-primary" id="processOffcanvas_close">
                        <span class="svg-icon svg-icon-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black"></rect>
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black"></rect>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body hover-scroll-overlay-y p-0">
                @if (isset($existingRequirement['processes']))
                <div class="accordion" id="processAccordion">
                    @foreach ($existingRequirement['processes'] as $process)
                    @php
                    $summary = $perProcessSummary[$process->id] ?? [
                    'day' => ['assigned' => 0, 'present' => 0, 'workers' => collect()],
                    'night' => ['assigned' => 0, 'present' => 0, 'workers' => collect()],
                    ];
                    $dayPresent = $summary['day']['present'] ?? 0;
                    $dayAssigned = $summary['day']['assigned'] ?? 0;
                    $nightPresent = $summary['night']['present'] ?? 0;
                    $nightAssigned = $summary['night']['assigned'] ?? 0;
                    @endphp
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $process->id }}">
                            <button class="accordion-button py-4 collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse-{{ $process->id }}"
                                aria-expanded="false" aria-controls="collapse-{{ $process->id }}">
                                <span class="fw-bold text-primary">{{ $process->name }}</span>
                                <strong class="ms-5 small text-muted">
                                    (Day: {{ $dayPresent }}/{{ $dayAssigned }}, Night:
                                    {{ $nightPresent }}/{{ $nightAssigned }})
                                </strong>
                            </button>
                        </h2>
                        <div id="collapse-{{ $process->id }}" class="accordion-collapse collapse"
                            aria-labelledby="heading-{{ $process->id }}" data-bs-parent="#processAccordion">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <p class="fw-bold mb-1">Day Shift</p>
                                        @php
                                        $sortedDayWorkers = (
                                        $summary['day']['workers'] ?? collect()
                                        )->sortBy(function ($worker) use ($lineId) {
                                        $isAbsent = in_array($worker->attendance_status, ['A']);
                                        $isShifted = $worker->line_id != $lineId;
                                        if ($isAbsent) {
                                        return 0;
                                        }
                                        if ($isShifted) {
                                        return 1;
                                        }
                                        return 2;
                                        });
                                        @endphp
                                        <ul class="list-group">
                                            @foreach ($sortedDayWorkers as $worker)
                                            @php
                                            $isPermanent = $worker->line_id == $lineId;
                                            $statusBadge = in_array($worker->attendance_status, [
                                            'P',
                                            'M/P',
                                            ])
                                            ? 'Present'
                                            : 'Absent';
                                            @endphp
                                            <li class="list-group-item position-relative d-flex p-0"
                                                @if ($isPermanent) style="background-color: #f0fdf4;"
                                                @else style="background-color: #c1dcff" @endif>

                                                <div
                                                    class="details px-3 py-2 d-flex flex-column gap-1 flex-grow-1">
                                                    <div>
                                                        <strong
                                                            class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }}">{{ $worker->name }}</strong>
                                                        <small
                                                            class="">({{ $worker->code }})</small>
                                                        <small
                                                            class="">-<strong>{{ optional($worker->level)->name ?? '-' }}</strong></small>
                                                    </div>

                                                    @if ($isPermanent)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="">{{ $worker?->line?->name }}
                                                            - {{ $worker?->process?->name }}</small>
                                                    </div>
                                                    @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="text-decoration-line-through">{{ $worker?->line?->name }}
                                                            - {{ $worker?->process?->name }}</small>
                                                        <i class="fa-solid fa-arrow-right"></i>
                                                        <small
                                                            class="text-primary">{{ $existingRequirement['line_name'] }}
                                                            -
                                                            {{ $worker->temp_process->name ?? ($worker?->process?->name ?? '-') }}</small>
                                                    </div>
                                                    @endif

                                                </div>

                                                <div class="d-flex flex-column align-items-stretch justify-content-center h-full"
                                                    style="border-left: 1px solid rgba(0, 0, 0, .125); font-size:0.75rem; min-width: 70px;">
                                                    <strong
                                                        class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0"
                                                        style="border-bottom: 1px solid rgba(0, 0, 0, 0.0625); margin-bottom:-1px;">
                                                        {{ $statusBadge }}
                                                    </strong>
                                                    <strong
                                                        class="{{ $isPermanent ? 'text-muted' : 'text-primary' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0 px-2"
                                                        style="border-top: 1px solid rgba(0, 0, 0, 0.0625);">
                                                        {{ $isPermanent ? 'Permanent' : 'Shifted' }}
                                                    </strong>
                                                </div>
                                            </li>
                                            @endforeach
                                            @if (($summary['day']['workers'] ?? collect())->isEmpty())
                                            <li class="list-group-item py-1 px-2 text-muted">No workers
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <p class="fw-bold mb-1">Night Shift</p>
                                        @php
                                        $sortedNightWorkers = (
                                        $summary['night']['workers'] ?? collect()
                                        )->sortBy(function ($worker) use ($lineId) {
                                        $isAbsent = in_array($worker->attendance_status, ['A']);
                                        $isShifted = $worker->line_id != $lineId;
                                        if ($isAbsent) {
                                        return 0;
                                        }
                                        if ($isShifted) {
                                        return 1;
                                        }
                                        return 2;
                                        });
                                        @endphp
                                        <ul class="list-group">
                                            @foreach ($sortedNightWorkers as $worker)
                                            @php
                                            $isPermanent = $worker->line_id == $lineId;
                                            $statusBadge = in_array($worker->attendance_status, [
                                            'P',
                                            'M/P',
                                            ])
                                            ? 'Present'
                                            : 'Absent';
                                            @endphp
                                            <li class="list-group-item position-relative d-flex p-0"
                                                @if ($isPermanent) style="background-color: #f0fdf4;"
                                                @else style="background-color: #c1dcff" @endif>

                                                <div
                                                    class="details px-3 py-2 d-flex flex-column gap-1 flex-grow-1">
                                                    <div>
                                                        <strong
                                                            class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }}">{{ $worker->name }}</strong>
                                                        <small
                                                            class="">({{ $worker->code }})</small>
                                                        <small
                                                            class="">-<strong>{{ optional($worker->level)->name ?? '-' }}</strong></small>
                                                    </div>

                                                    @if ($isPermanent)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="">{{ $worker?->line?->name }}
                                                            - {{ $worker?->process?->name }}</small>
                                                    </div>
                                                    @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <small
                                                            class="text-decoration-line-through">{{ $worker?->line?->name }}
                                                            - {{ $worker?->process?->name }}</small>
                                                        <i class="fa-solid fa-arrow-right"></i>
                                                        <small
                                                            class="text-primary">{{ $existingRequirement['line_name'] }}
                                                            -
                                                            {{ $worker->temp_process->name ?? ($worker?->process?->name ?? '-') }}</small>
                                                    </div>
                                                    @endif

                                                </div>

                                                <div class="d-flex flex-column align-items-stretch justify-content-center h-full"
                                                    style="border-left: 1px solid rgba(0, 0, 0, .125); font-size:0.75rem; min-width: 70px;">
                                                    <strong
                                                        class="{{ $statusBadge == 'Present' ? 'text-success' : 'text-danger' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0"
                                                        style="border-bottom: 1px solid rgba(0, 0, 0, 0.0625); margin-bottom:-1px;">
                                                        {{ $statusBadge }}
                                                    </strong>
                                                    <strong
                                                        class="{{ $isPermanent ? 'text-muted' : 'text-primary' }} flex-grow-1 d-flex justify-content-center align-items-center rounded-0 px-2"
                                                        style="border-top: 1px solid rgba(0, 0, 0, 0.0625);">
                                                        {{ $isPermanent ? 'Permanent' : 'Shifted' }}
                                                    </strong>
                                                </div>
                                            </li>
                                            @endforeach
                                            @if (($summary['night']['workers'] ?? collect())->isEmpty())
                                            <li class="list-group-item py-1 px-2 text-muted">No workers
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>



    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('.worker_ids').select2({
                    width: '100%',
                    templateResult: formatWorkerOption,
                    templateSelection: formatWorkerSelection,
                    matcher: workerMatcher,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });

                $(document).on('click', '#edit-button', function(e) {
                    e.preventDefault();
                    $(this).hide();
                    $('#submit-button').removeClass('d-none');
                    $('#create_department').find('input').removeAttr('readonly');
                })

                function formatWorkerOption(option) {
                    if (!option.id) return option.text;

                    const name = $(option.element).data('name');
                    const code = $(option.element).data('code');
                    const level = $(option.element).data('level');
                    const line = $(option.element).data('line');
                    const status = $(option.element).data('status');

                    return `
                        <div style="line-height: 1; display: flex; flex-direction: column; gap: 5px; padding: 2px 5px 2px 0px;">
                            <span style="display: flex; gap: 5px;">
                                <strong >${name}</strong>
                                <small>(${code})</small>
                                <small><strong>${level}</strong></small>
                                <small class="${status=='Present' ? 'text-success' : 'text-danger'}">${status}</small>
                            </span>
                            <small>Currently assinged to: ${line}</small>
                        </div>
                    `;
                }

                function formatWorkerSelection(option) {
                    if (!option.id) return option.text;

                    const name = $(option.element).data('name');
                    const code = $(option.element).data('code');
                    const level = $(option.element).data('level');
                    const line = $(option.element).data('line');
                    const status = $(option.element).data('status');

                    return `
                        <div style="line-height: 1; display: flex; flex-direction: column; gap: 5px; padding: 2px 5px 2px 0px;">
                            <span style="display: flex; gap: 5px;">
                                <strong >${name}</strong>
                                <small>(${code})</small>
                                <small><strong>${level}</strong></small>
                                <small class="${status=='Present' ? 'text-success' : 'text-danger'}">${status}</small>
                            </span>
                            <small>Currently assinged to: ${line}</small>
                        </div>
                    `;
                }

                function workerMatcher(params, data) {
                    if (!params.term || !data.element) return data;
                    const search = params.term.toLowerCase();
                    const name = String($(data.element).data('name') || '');
                    const code = String($(data.element).data('code') || '');
                    const level = String($(data.element).data('level') || '');
                    const line = String($(data.element).data('line') || '');
                    const status = String($(data.element).data('status') || '');
                    if (
                        name.toLowerCase().includes(search) ||
                        code.toLowerCase().includes(search) ||
                        level.toLowerCase().includes(search) ||
                        line.toLowerCase().includes(search) ||
                        status.toLowerCase().includes(search)
                    ) {
                        return data;
                    }
                    return null;
                }


                $(document).on("submit", ".addWorkersToLine", function(e) {
                    e.preventDefault();
                    const line_id = $(this).find('input[name="line_id"]').val();
                    const worker_ids = $(this).find('select[name="worker_ids[]"]').val();
                    const start_date = $(this).find('input[name="start_date"]').val();
                    const end_date = $(this).find('input[name="end_date"]').val();
                    const shift_id = $(this).find('select[name="shift_id"]').val();
                    const process_id = $(this).find('select[name="process_id"]').val();

                    console.log(line_id, worker_ids, start_date, end_date, shift_id, process_id);
                    addWorkersToLine(line_id, worker_ids, start_date, end_date, shift_id, process_id);
                })

                function addWorkersToLine(line_id, worker_ids, start_date, end_date, shift_id, process_id) {
                    $.ajax({
                        url: "{{ route('production.planning.assign-to-line') }}",
                        type: "POST",
                        data: {
                            worker_ids: worker_ids,
                            line_id: line_id,
                            start_date: start_date,
                            end_date: end_date,
                            shift_id: shift_id,
                            process_id: process_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log("Assigned successfully", response);
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert("Error: " + xhr.responseText || error);
                        }
                    });
                }

                $(document).on('click', '.remove-worker', function(e) {
                    e.preventDefault();
                    const worker_id = $(this).data('id');
                    const line_id = "{{ $lineId }}";
                    removeWorkerFromLine(worker_id, line_id);
                });

                function removeWorkerFromLine(worker_id, line_id) {
                    $.ajax({
                        url: "{{ route('production.planning.remove-from-line') }}",
                        type: "POST",
                        data: {
                            worker_id: worker_id,
                            line_id: line_id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            console.log("Removed successfully", data);
                            window.location.reload();
                        },
                        error: function(xhr) {
                            alert("Remove Error: " + xhr.responseText);
                        }
                    });
                }


                function calculateShiftTotal(shift) {
                    let total = 0;
                    $(`.shift-input[data-shift="${shift}"]`).each(function() {
                        const val = parseInt($(this).val());
                        if (!isNaN(val)) {
                            total += val;
                        }
                    });
                    $(`#${shift}_total`).text(`( Total ${total} )`);
                }

                $(document).on('input', '.shift-input', function() {
                    const shift = $(this).data('shift');
                    calculateShiftTotal(shift);
                });

            })
        </script>
    </x-slot>

</x-app-layout>