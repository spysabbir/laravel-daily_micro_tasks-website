@extends('layouts.template_master')

@section('title', 'Deposit Request (Pending)')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Deposit Request (Pending)</h3>
                <h3>Total: <span id="total_deposits_count">0</span></h3>
                <div class="action-btn">
                    @can('deposit.request.store')
                    <!-- Normal Deposit Modal -->
                    <button type="button" class="btn btn-primary m-1 btn-xs" data-bs-toggle="modal" data-bs-target=".createModel">Deposit <i data-feather="plus-circle"></i></button>
                    @endcan
                    <div class="modal fade createModel select2Model" tabindex="-1" aria-labelledby="createModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModelLabel">Deposit</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="createForm">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">User Name <span class="text-danger">*</span></label>
                                            <select class="form-select js-select2-single" id="user_id" name="user_id" required data-width="100%">
                                                <option value="">-- Select User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }} </option>
                                                @endforeach
                                            </select>
                                            <span class="text-danger error-text user_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="method" class="form-label">Deposit Method <span class="text-danger">*</span></label>
                                            <select class="form-select" id="method" name="method" required>
                                                <option value="">-- Select Deposit Method --</option>
                                                <option value="Bkash">Bkash</option>
                                                <option value="Nagad">Nagad</option>
                                                <option value="Rocket">Rocket</option>
                                            </select>
                                            <span class="text-danger error-text method_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="number" class="form-label">Deposit Number <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="number" name="number" placeholder="Deposit Number" required>
                                            <small class="text-info d-block">Note: The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).</small>
                                            <span class="text-danger error-text number_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transaction_id" class="form-label">Transaction Id <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="transaction_id" name="transaction_id" placeholder="Transaction Id" required>
                                            <span class="text-danger error-text transaction_id_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Deposit Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="amount" name="amount" min="20" placeholder="Deposit Amount" required>
                                                <span class="input-group-text input-group-addon">{{ get_site_settings('site_currency_symbol') }}</span>
                                            </div>
                                            <small class="text-info d-block">Note: Minimum deposit amount is {{ get_site_settings('site_currency_symbol') }} 20.</small>
                                            <span class="text-danger error-text amount_error"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Deposit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_user_id" class="form-label">User Id</label>
                            <input type="number" id="filter_user_id" class="form-control filter_data" placeholder="Search User Id">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="method" class="form-label">Method</label>
                            <select class="form-select filter_data" id="filter_method">
                                <option value="">-- Select Deposit Method --</option>
                                <option value="Bkash">Bkash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_number" class="form-label">Number</label>
                            <input type="number" id="filter_number" class="form-control filter_data" placeholder="Search Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_transaction_id" class="form-label">Transaction Id</label>
                            <input type="text" id="filter_transaction_id" class="form-control filter_data" placeholder="Search Transaction Id">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="pendingDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User Id</th>
                                <th>User Name</th>
                                <th>Method</th>
                                <th>Number</th>
                                <th>Transaction Id</th>
                                <th>Amount</th>
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
                url: "{{ route('backend.deposit.request') }}",
                data: function (e) {
                    e.method = $('#filter_method').val();
                    e.user_id = $('#filter_user_id').val();
                    e.number = $('#filter_number').val();
                    e.transaction_id = $('#filter_transaction_id').val();
                },
                dataSrc: function (json) {
                    // Update total deposit count
                    $('#total_deposits_count').text(json.totalDepositsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user_id', name: 'user_id' },
                { data: 'user_name', name: 'user_name' },
                { data: 'method', name: 'method' },
                { data: 'number', name: 'number' },
                { data: 'transaction_id', name: 'transaction_id' },
                { data: 'amount', name: 'amount' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });
        // Filter Data
        $('.filter_data').keyup(function(){
            $('#pendingDataTable').DataTable().ajax.reload();
        });

        // Store Data
        $('#createForm').submit(function(event) {
            event.preventDefault();

            var submitButton = $(this).find("button[type='submit']");
            submitButton.prop("disabled", true).text("Submitting...");

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ route('backend.deposit.request.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend:function(){
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        })
                    }else{
                        if (response.status == 401) {
                            toastr.error(response.error);
                        }else{
                            $('.createModel').modal('hide');
                            $('#createForm')[0].reset();
                            $('#pendingDataTable').DataTable().ajax.reload();
                            toastr.success('Deposit request sent successfully.');
                        }
                    }
                },
                complete: function() {
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('backend.deposit.request.show', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);

                    $('.viewModal').modal('show');
                },
            });
        });

        // Update Data
        $("body").on("submit", "#editForm", function(e){
            e.preventDefault();

            var id = $('#deposit_id').val();
            var url = "{{ route('backend.deposit.request.status.change', ":id") }}";
            url = url.replace(':id', id)

            // Disable the submit button to prevent multiple submissions
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
                        if (response.status == 401) {
                            $(".viewModal").modal('hide');
                            $('#pendingDataTable').DataTable().ajax.reload();
                            toastr.info(response.error);
                        } else {
                            $('#pendingDataTable').DataTable().ajax.reload();
                            $(".viewModal").modal('hide');
                            toastr.success('Deposit status change successfully.');
                        }
                    }
                },
                complete: function() {
                    // Re-enable the submit button after the request completes
                    submitButton.prop("disabled", false).text("Submit");
                }
            });
        });
    });
</script>
@endsection

