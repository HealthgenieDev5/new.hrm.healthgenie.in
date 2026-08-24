<x-app-layout>

    <x-slot name="pageTitle">
        {{ $pageTitle }}
    </x-slot>

    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">
            <div class="row g-5">
                <div class="col-xl-4">
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Lead Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="text-muted fs-8">Phone Number</div>
                                <div class="fw-bold fs-5">{{ $callRecord->phone_number }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted fs-8">Call Type</div>
                                <div class="fw-semibold">{{ ucfirst($callRecord->call_type) }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted fs-8">Duration</div>
                                <div class="fw-semibold">{{ $callRecord->duration }} sec</div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted fs-8">Call Timestamp</div>
                                <div class="fw-semibold">{{ $callRecord->call_timestamp?->format('d M Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Add Disposition</h3>
                        </div>
                        <div class="card-body">
                            <form id="lead_disposition_form">
                                <div class="mb-5">
                                    <label for="disposition" class="form-label required">Disposition</label>
                                    <select name="disposition" id="disposition" class="form-select form-select-sm"
                                        data-control="select2" data-placeholder="Select disposition">
                                        <option value=""></option>
                                        @foreach ($dispositions as $disposition)
                                            <option value="{{ $disposition }}">{{ ucfirst($disposition) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-5">
                                    <label for="sub_disposition" class="form-label">Sub Disposition</label>
                                    <select name="sub_disposition" id="sub_disposition" class="form-select form-select-sm"
                                        data-control="select2" data-placeholder="Select sub disposition" data-allow-clear="true">
                                        <option value=""></option>
                                        @foreach ($subDispositions as $subDisposition)
                                            <option value="{{ $subDisposition }}">{{ ucfirst($subDisposition) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-5">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control form-control-sm" rows="4"
                                        placeholder="Add remarks about this lead"></textarea>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <button type="submit" id="save_disposition_btn" class="btn btn-primary btn-sm">Save Disposition</button>
                                    <span id="disposition_feedback" class="text-muted fs-8"></span>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Disposition Timeline</h3>
                        </div>
                        <div class="card-body">
                            @if ($callRecord->dispositions->isEmpty())
                                <div class="text-muted">No dispositions have been added yet.</div>
                            @else
                                <div class="timeline-label">
                                    @foreach ($callRecord->dispositions as $disposition)
                                        <div class="timeline-item">
                                            <div class="timeline-label fw-bold text-gray-800 fs-7">
                                                {{ $disposition->disposed_at?->format('d M Y h:i A') }}
                                            </div>
                                            <div class="timeline-badge">
                                                <i class="fa fa-genderless text-primary fs-1"></i>
                                            </div>
                                            <div class="timeline-content ps-3">
                                                <div class="fw-bold text-gray-900">
                                                    {{ ucfirst($disposition->disposition) }}
                                                    @if ($disposition->sub_disposition)
                                                        <span class="text-muted">/ {{ ucfirst($disposition->sub_disposition) }}</span>
                                                    @endif
                                                </div>
                                                @if ($disposition->remarks)
                                                    <div class="text-muted mt-1">{{ $disposition->remarks }}</div>
                                                @endif
                                                <div class="text-gray-600 fs-8 mt-1">
                                                    By {{ $disposition->disposedBy?->name ?? 'Unknown user' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                $('#lead_disposition_form').on('submit', function(e) {
                    e.preventDefault();

                    $('#save_disposition_btn').prop('disabled', true);
                    $('#disposition_feedback').removeClass('text-success text-danger').text('Saving...');

                    $.ajax({
                        url: "{{ route('dashboard.synced-call-records.dispositions.store', $callRecord->id) }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            disposition: $('#disposition').val(),
                            sub_disposition: $('#sub_disposition').val(),
                            remarks: $('#remarks').val()
                        },
                        success: function(response) {
                            $('#disposition_feedback').addClass('text-success').text(response.message);
                            window.location.reload();
                        },
                        error: function(xhr) {
                            let message = 'Unable to save disposition.';

                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }

                            $('#save_disposition_btn').prop('disabled', false);
                            $('#disposition_feedback').addClass('text-danger').text(message);
                        }
                    });
                });
            });
        </script>
    </x-slot>

</x-app-layout>
