@extends('layouts.template_master')

@section('title', 'Working Task List - Pending')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h3 class="card-title">Working Task List - Pending</h3>
                    <p class="text-info">
                        <strong>Note:</strong> You can see only the last 5 days of data.
                    </p>
                </div>
                <div>
                    <a href="{{ route('worked_task.list.approved') }}" class="btn btn-success btn-xs m-1">Approved</a>
                    <a href="{{ route('worked_task.list.rejected') }}" class="btn btn-danger btn-xs m-1">Rejected</a>
                </div>
            </div>
            <div class="card-body">
                <div class="filter mb-3 border p-2">
                    <div class="row">
                        <div class="col-xl-3 col-lg-5 mb-3">
                            <div class="form-group">
                                <label class="form-label" for="filter_date">Filter By Date</label>
                                <input type="date" class="form-control filter_data" id="filter_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>Task Title</th>
                                <th>You Earn</th>
                                <th>Submit Date</th>
                            </tr>
                        </thead>
                        <tbody>

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
                url: "{{ route('worked_task.list.pending') }}",
                data: function (d) {
                    d.filter_date = $('#filter_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'title', name: 'title' },
                { data: 'earnings_from_work', name: 'earnings_from_work' },
                { data: 'created_at', name: 'created_at' },
            ]
        });

        // Filter Data
        $('.filter_data').change(function(){
            $('#allDataTable').DataTable().ajax.reload();
        });

        // Set Date Range
        var today = new Date();
        var beforeDays = new Date();
        beforeDays.setDate(today.getDate() - 4);
        $('#filter_date').attr('max', today.toISOString().split('T')[0]);
        $('#filter_date').attr('min', beforeDays.toISOString().split('T')[0]);

    });
</script>
@endsection
