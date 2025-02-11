<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Deposit Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>User ID</td>
                                <td>{{ $deposit->user->id }}</td>
                            </tr>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $deposit->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $deposit->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Method</td>
                                <td>{{ $deposit->method }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Number</td>
                                <td>{{ $deposit->number }}</td>
                            </tr>
                            <tr>
                                <td>Transaction ID</td>
                                <td>{{ $deposit->transaction_id }}</td>
                            </tr>
                            <tr>
                                <td>Deposit Amount</td>
                                <td>
                                    <span class="badge bg-primary">{{ get_site_settings('site_currency_symbol') . ' ' .$deposit->amount }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Submitted Date</td>
                                <td>{{ $deposit->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @if ($deposit->status == 'Approved')
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Approved!</h4>
                <p>This deposit request has been approved by <strong>{{ $deposit->approvedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($deposit->approved_at)) }}</strong></p>
            </div>
        @elseif ($deposit->status == 'Rejected')
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Rejected!</h4>
                <p>This deposit request has been rejected by <strong>{{ $deposit->rejectedBy->name }}</strong> at <strong>{{ date('d M, Y  h:i:s A', strtotime($deposit->rejected_at)) }}</strong></p>
                <p><strong>Rejected Reason:</strong> {{ $deposit->rejected_reason }}</p>
            </div>
        @else
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">Pending!</h4>
                <p>This deposit request is pending for approval.</p>
            </div>
            @can('deposit.request.check')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        Change Status
                    </h4>
                </div>
                <div class="card-body">
                    <form class="forms-sample" id="editForm">
                        @csrf
                        <input type="hidden" id="deposit_id" value="{{ $deposit->id }}">
                        <div class="mb-3">
                            <label for="deposit_status" class="form-label">Deposit Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="deposit_status" name="status">
                                <option value="">-- Select Status --</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                            <span class="text-danger error-text update_status_error"></span>
                        </div>
                        <div class="mb-3" id="rejected_reason_div" style="display: none;">
                            <label for="deposit_rejected_reason" class="form-label">Rejected Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="deposit_rejected_reason" name="rejected_reason" rows="4" placeholder="Rejected Reason"></textarea>
                            <span class="text-danger error-text update_rejected_reason_error"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
            @endcan
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#deposit_status').change(function() {
            var status = $(this).val();
            if (status == 'Rejected') {
                $('#rejected_reason_div').show();
            } else {
                $('#rejected_reason_div').hide();
            }
        });
    });
</script>
