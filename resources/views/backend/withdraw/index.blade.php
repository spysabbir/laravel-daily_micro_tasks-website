@extends('layouts.template_master')

@section('title', 'Withdraw Request (Pending)')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Withdraw Request (Pending)</h3>
                <div class="action-btn">
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target=".rejectedData">
                        Rejected Request
                    </button>
                    <!-- Withdraw Request (Reject) Modal -->
                    <div class="modal fade rejectedData" tabindex="-1" aria-labelledby="rejectedDataLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectedDataLabel">Withdraw Reject</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table id="rejectedDataTable" class="table w-100">
                                            <thead>
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>User Id</th>
                                                    <th>User Name</th>
                                                    <th>Type</th>
                                                    <th>Method</th>
                                                    <th>Number</th>
                                                    <th>Withdraw Amount</th>
                                                    <th>Payable Amount</th>
                                                    <th>Submitted Date</th>
                                                    <th>Rejected Reason</th>
                                                    <th>Rejected By</th>
                                                    <th>Rejected At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_type" class="form-label">Withdraw Type</label>
                                <select class="form-select filter_data" id="filter_type">
                                    <option value="">-- Select Withdraw Type --</option>
                                    <option value="Ragular">Ragular</option>
                                    <option value="Instant">Instant</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filter_method" class="form-label">Deposit Method</label>
                                <select class="form-select filter_data" id="filter_method">
                                    <option value="">-- Select Deposit Method --</option>
                                    <option value="Bkash">Bkash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pendingDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Withdraw Id</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Type</th>
                                <th>Method</th>
                                <th>Number</th>
                                <th>Withdraw Amount</th>
                                <th>Payable Amount</th>
                                <th>Submitted Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">View</h5>
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

        // Pending Data
        $('#pendingDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.withdraw.request') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.type = $('#filter_type').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'id', name: 'id' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'type', name: 'type' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.withdraw.request.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();

            var id = $('#withdraw_id').val();
            var url = "{{ route('backend.withdraw.request.status.change', ":id") }}";
            url = url.replace(':id', id)

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            $.ajax({
                url: url,
                type: "PUT",
                data: $(this).serialize(),
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function (response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.update_'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        $("#editForm")[0].reset();
                        $(".rejectedData").modal('hide');
                        $('#pendingDataTable').DataTable().ajax.reload();
                        $('#rejectedDataTable').DataTable().ajax.reload();
                        $(".viewModal").modal('hide');
                        toastr.success('Withdraw status change successfully.');
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        })

        // Rejected Data
        $('#rejectedDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('backend.withdraw.request.rejected') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'type', name: 'type' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'amount', name: 'amount' },
                { data: 'payable_amount', name: 'payable_amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'rejected_reason', name: 'rejected_reason' },
                { data: 'rejected_by', name: 'rejected_by' },
                { data: 'rejected_at', name: 'rejected_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Delete Data
        $(document).on('click', '.deleteBtn', function(){
            var id = $(this).data('id');
            var url = "{{ route('backend.withdraw.request.delete', ":id") }}";
            url = url.replace(':id', id)
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        success: function(response) {
                            $(".rejectedData").modal('hide');
                            $('#rejectedDataTable').DataTable().ajax.reload();
                            toastr.success('Withdraw request delete successfully.');
                        }
                    });
                }
            })
        })
    });
</script>
@endsection

