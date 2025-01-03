@extends('layouts.template_master')

@section('title', 'Block List')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="text">
                    <h3 class="card-title">Block List</h3>
                    <h3>Total: <span id="total_blockeds_count">0</span></h3>
                    <p class="card-description text-info">
                        Note: Hi user, This is the block list so when you block buyers you can see the blocked buyers here. You can block or unblock buyers at any time. When you will block the buyer you will not be able to see the tasks posted by the buyer but if you unblock then again you will see the tasks posted by the buyer. Please contact us if you face any problem.
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allDataTable" class="table">
                        <thead>
                            <tr>
                                <th>Sl No</th>
                                <th>User</th>
                                <th>Blocked At</th>
                                <th>Action</th>
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
                url: "{{ route('block_list') }}",
                dataSrc: function (json) {
                    // Update total blocked count
                    $('#total_blockeds_count').text(json.totalBlockedsCount);
                    return json.data;
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'user', name: 'user' },
                { data: 'blocked_at', name: 'blocked_at' },
                { data: 'action', name: 'action' }
            ]
        });
    });
</script>
@endsection
