@extends('layouts.template_master')

@section('title', 'Task List - Reviewed')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Task Details - ID: {{ $postTask->id }}</h3>
                </div>
                <div class="action">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-warning">Back</a>
                </div>
            </div>
            <div class="card-body">
                <p class="border p-1 m-1">
                    <strong class="text-info">User Id: </strong>{{ $postTask->user->id }},
                    <strong class="text-info">User Name: </strong>{{ $postTask->user->name }},
                    <strong class="text-info">User Email: </strong>{{ $postTask->user->email }}
                </p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Category: </strong>{{ $postTask->category->name }},
                    <strong class="text-info">Sub Category: </strong>{{ $postTask->subCategory->name }},
                    <strong class="text-info">Child Category: </strong>{{ $postTask->childCategory->name ?? 'N/A' }}
                </p>
                <p class="border p-1 m-1"><strong class="text-info">Title: </strong>{{ $postTask->title }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Description: </strong>{{ $postTask->description }}</p>
                <p class="border p-1 m-1"><strong class="text-info">Required Proof Answer: </strong>{{ $postTask->required_proof_answer }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Required Proof Photo: </strong>
                    Total: {{ $postTask->required_proof_photo }} Required Proof Photo{{ $postTask->required_proof_photo > 1 ? 's' : '' }}
                </p>
                <p class="border p-1 m-1"><strong class="text-info">Additional Note: </strong>{{ $postTask->additional_note }}</p>
                <p class="border p-1 m-1">
                    <strong class="text-info">Submited At: </strong>{{ $postTask->created_at->format('d F, Y h:i:s A') }},
                    <strong class="text-info">Approved At: </strong>{{ date('d F, Y h:i:s A', strtotime($postTask->approved_at)) }}
                </p>
                {{-- <div class="my-3">
                    @php
                        $proofSubmittedCount = $proofSubmitted->count();
                        $proofStyleWidth = $proofSubmittedCount != 0 ? round(($proofSubmittedCount / $postTask->worker_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmittedCount == 0 ? 'primary' : 'success';
                    @endphp
                    <p class="mb-1"><strong class="text-info">Proof Status: </strong> <span class="text-success">Submit: {{ $proofSubmittedCount }}</span>, Need: {{ $postTask->worker_needed }}</p>
                    <div class="progress position-relative">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated bg-{{ $progressBarClass }}" role="progressbar" style="width: {{ $proofStyleWidth }}%" aria-valuenow="{{ $proofSubmittedCount }}" aria-valuemin="0" aria-valuemax="{{ $postTask->worker_needed }}"></div>
                        <span class="position-absolute w-100 text-center">{{ $proofSubmittedCount }} / {{ $postTask->worker_needed }}</span>
                    </div>
                </div>
                <div class="my-3">
                    @php
                        $pendingProofCount = $proofSubmitted->where('status', 'Pending')->count();
                        $approvedProofCount = $proofSubmitted->where('status', 'Approved')->count();
                        $rejectedProofCount = $proofSubmitted->where('status', 'Rejected')->count();
                        $reviewedProofCount = $proofSubmitted->where('status', 'Reviewed')->count();

                        $totalProof = $approvedProofCount + $rejectedProofCount + $reviewedProofCount + $pendingProofCount;

                        $pendingProofStyleWidth = $totalProof != 0 ? round(($pendingProofCount / $totalProof) * 100, 2) : 100;
                        $approvedProofStyleWidth = $totalProof != 0 ? round(($approvedProofCount / $totalProof) * 100, 2) : 100;
                        $rejectedProofStyleWidth = $totalProof != 0 ? round(($rejectedProofCount / $totalProof) * 100, 2) : 100;
                        $reviewedProofStyleWidth = $totalProof != 0 ? round(($reviewedProofCount / $totalProof) * 100, 2) : 100;
                    @endphp
                    <p class="mb-1"><strong class="text-info">Check Status: </strong> <span class="text-primary">Pending: {{ $pendingProofCount }}</span>, <span class="text-success">Approved: {{ $approvedProofCount }}</span>, <span class="text-danger">Rejected: {{ $rejectedProofCount }}</span>, <span class="text-warning">Reviewed: {{ $reviewedProofCount }}</span></p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: {{ $pendingProofStyleWidth }}%" aria-valuenow="{{ $pendingProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $pendingProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: {{ $approvedProofStyleWidth }}%" aria-valuenow="{{ $approvedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $approvedProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: {{ $rejectedProofStyleWidth }}%" aria-valuenow="{{ $rejectedProofStyleWidth }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $rejectedProofCount }} / {{ $totalProof }}</div>
                        <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: {{ $reviewedProofStyleWidth }}%" aria-valuenow="{{ $reviewedProofCount }}" aria-valuemin="0" aria-valuemax="{{ $totalProof }}">{{ $reviewedProofCount }} / {{ $totalProof }}</div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">Proof Task List</h3>
                    <h4 class="text-primary">Total Reviewed: <span id="reviewed_proof_tasks_count">0</span></h4>
                    <h4 class="text-warning">Running Reviewed: <span id="running_reviewed_proof_tasks_count">0</span></h4>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_worked_task_id" class="form-label">Worked Task Id</label>
                                <input type="number" id="filter_worked_task_id" class="form-control filter_data" placeholder="Search Worked Task Id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_user_id" class="form-label">User Id</label>
                                <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Worked Task Id</th>
                                <th>User Details</th>
                                {{-- <th>Proof Answer</th> --}}
                                {{-- <th>Submited Date</th> --}}
                                <th>Rejected Date</th>
                                <th>Reviewed Date</th>
                                <th>Checking Expired Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal viewSingleTaskProofModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">Check</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="modalBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Reviewed Modal -->
                            <div class="modal fade reviewedModal" tabindex="-1" aria-labelledby="reviewedModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="reviewedModalLabel">Reviewed</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                        </div>
                                        <div class="modal-body" id="reviewedModalBody">

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            // serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.reviewed.worked_task_view', encrypt($postTask->id)) }}",
                type: "GET",
                data: function (d) {
                    d.worked_task_id = $('#filter_worked_task_id').val();
                    d.user_id = $('#filter_user_id').val();
                },
                dataSrc: function (json) {
                    // Update total count
                    $('#reviewed_proof_tasks_count').text(json.reviewedProofTasksCount);
                    $('#running_reviewed_proof_tasks_count').text(json.reviewedProofTasksCountRunning);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'user', name: 'user' },
                // { data: 'proof_answer', name: 'proof_answer' },
                // { data: 'created_at', name: 'created_at' },
                { data: 'rejected_at', name: 'rejected_at' },
                { data: 'reviewed_at', name: 'reviewed_at' },
                { data: 'checking_expired_time', name: 'checking_expired_time' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').keyup(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Check Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.worked_task.proof_check', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    // Destroy the existing LightGallery instance if it exists
                    var lightGalleryInstance = $('#backend-single-lightgallery').data('lightGallery');
                    if (lightGalleryInstance) {
                        lightGalleryInstance.destroy(true); // Pass `true` to completely remove DOM bindings
                    }

                    // Reinitialize LightGallery
                    $('#backend-single-lightgallery').lightGallery({
                        share: false,
                        showThumbByDefault: false,
                        hash: false,
                        mousewheel: false,
                    });

                    $('.viewModal').modal('show');
                },
            });
        });

        // Check Reviewed Data
        $(document).on('click', '.reviewedBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.worked_task.reviewed_check', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#reviewedModalBody').html(response);

                    // Destroy the existing LightGallery instance if it exists
                    var lightGalleryInstance = $('#single-lightgallery-reviewed').data('lightGallery');
                    if (lightGalleryInstance) {
                        lightGalleryInstance.destroy(true); // Pass `true` to completely remove DOM bindings
                    }

                    // Reinitialize LightGallery
                    $('#single-lightgallery-reviewed').lightGallery({
                        share: false,
                        showThumbByDefault: false,
                        hash: false,
                        mousewheel: false,
                    });

                    // Show Modal
                    $('.reviewedModal').modal('show');
                },
            });
        });

        $(document).on('onCloseAfter.lg', '#single-lightgallery', function () {
            // Remove hash fragment from the URL
            const url = window.location.href.split('#')[0];
            window.history.replaceState(null, null, url);
        });
    });
</script>
@endsection
