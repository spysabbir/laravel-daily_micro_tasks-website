<div class="card">
    <div class="card-header">
        <h3 class="card-title">ID: {{ $report->id }}</h3>
        <h3 class="card-title">Type: {{ $report->type }}</h3>
        <h3 class="card-title">Status - {{ $report->status }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <h5 class="text-primary">Reported User ID: {{ $report->reported->id }}, Reported User Name: {{ $report->reported->name }}</h5>
                <div class="mb-3">
                    <p class="my-2">Reason: {{ $report->reason }}</p>
                    <div>
                        <strong>Report By User ID: {{ $report->reportedBy->id }}</strong><br>
                        <strong>Report By User Name: {{ $report->reportedBy->name }}</strong><br>
                        <strong>Report At: {{ $report->created_at->format('d M, Y h:i A') }}</strong>
                    </div>
                    @if ($report->post_task_id)
                        <div class="border p-2">
                            <strong class="mb-2">Post Task Id:</strong> {{ $report->post_task_id }}<br>
                            <strong class="mb-2">Post Task Status:</strong> {{ $report->postTask->status }}<br>
                        </div>
                    @endif
                    @if ($report->proof_task_id)
                        <div class="border p-2">
                            <strong class="mb-2">Proof Task Id:</strong> {{ $report->proof_task_id }}<br>
                            <strong class="mb-2">Proof Task Status:</strong> {{ $report->proofTask->status }}<br>
                        </div>
                    @endif
                    @if ($report->photo)
                    <img src="{{ asset('uploads/report_photo') }}/{{ $report->photo }}" alt="Report Photo" class="img-fluid">
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                @if ($report->status == 'Pending')
                <form class="forms-sample" id="replyForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                    <div class="mb-3">
                        <label for="reply" class="form-label">Reply <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply" name="reply" rows="4" placeholder="Reply"></textarea>
                        <span class="text-danger error-text reply_error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="reply_photo" class="form-label">Reply Photo</label>
                        <input type="file" class="form-control" id="reply_photo" name="reply_photo" accept=".jpg, .jpeg, .png">
                        <span class="text-danger error-text reply_photo_error d-block"></span>
                        <img src="" alt="Photo" id="photoPreview" class="mt-2" style="display: none; width: 100px; height: 100px;">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                @else
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Resolved</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Reply:</strong> {{ $report_reply->reply }}<br>
                            <strong>Resolved By:</strong> {{ $report_reply->resolvedBy->name }}<br>
                            <strong>Resolved At:</strong> {{ date('d M, Y h:i A', strtotime($report_reply->resolved_at)) }}<br>
                            @if ($report_reply->reply_photo)
                            <img src="{{ asset('uploads/report_photo') }}/{{ $report_reply->reply_photo }}" alt="Reply Photo" class="img-fluid my-3">
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



