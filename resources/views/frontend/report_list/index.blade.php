@extends('layouts.template_master')

@section('title', 'Report List')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Report List</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="form-group">
                                <label for="filter_status" class="form-label">Status</label>
                                <select class="form-select filter_data" id="filter_status">
                                    <option value="">-- Select Status --</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Resolved">Resolved</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Type</th>
                                <th>User</th>
                                <th>Created At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- View Modal -->
                            <div class="modal fade viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
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

        // Read Data
        $('#allDataTable').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: {
                url: "{{ route('report_list') }}",
                data: function (e) {
                    e.status = $('#filter_status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'type', name: 'type' },
                { data: 'user', name: 'user' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action' }
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // View Data
        $(document).on('click', '.viewBtn', function () {
            var id = $(this).data('id');
            var url = "{{ route('report_view', ":id") }}";
            url = url.replace(':id', id)
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $('#modalBody').html(response);
                },
            });
        });
    });
</script>
@endsection
