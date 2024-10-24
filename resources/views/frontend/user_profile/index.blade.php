@extends('layouts.template_master')

@section('title', 'User Profile')

@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="position-relative">
                <figure class="overflow-hidden mb-0 d-flex justify-content-center">
                    <img src="{{ asset('template/images/others/profile_cover.jpg') }}" class="rounded-top" alt="profile cover">
                </figure>
                <div class="d-flex justify-content-between align-items-center position-absolute top-90 w-100 px-2 px-md-4 mt-n4">
                    <div class="d-flex">
                        <img class="wd-70 rounded-circle" id="userProfilePhotoPreview" src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="profile">
                        <div>
                            <h4 class="ms-3 text-dark">{{ $user->name }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom border-start border-end p-3">
                <div class="d-flex align-items-center">
                    <i class="me-1 icon-md text-primary" data-feather="columns"></i>
                    <span class="pt-1px d-none d-md-block text-primary">
                        Profile Information
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center mx-2" data-bs-toggle="modal" data-bs-target=".reportModal">
                        <i class="icon-md" data-feather="message-circle"></i>
                        <span class="d-none d-md-block ms-1">Report User</span>
                    </button>
                    <!-- Report Modal -->
                    <div class="modal fade reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="reportModalLabel">Report</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
                                </div>
                                <form class="forms-sample" id="reportForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="reason" class="form-label">Reason</label>
                                            <textarea class="form-control" id="reason" name="reason" placeholder="Reason"></textarea>
                                            <span class="text-danger error-text reason_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="photo" class="form-label">Photo</label>
                                            <input type="file" class="form-control" id="photo" name="photo">
                                            <span class="text-danger error-text photo_error"></span>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('block.unblock.user', $user->id) }}" class="btn btn-{{ $blocked ? 'danger' : 'warning' }} btn-sm d-flex align-items-center">
                        <i class="icon-md" data-feather="shield"></i>
                        <span class="d-none d-md-block ms-1">
                            {{ $blocked ? 'Unblock User' : 'Block User' }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row profile-body">
    <div class="col-md-4">
        <div class="card rounded">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">About</h6>
                </div>
                <hr>
                <div>
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Username:</label>
                    <p class="text-muted">
                        {{ $user->username ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Bio:</label>
                    <p class="text-muted">
                        {{$user->bio ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Last Login:</label>
                    <p class="text-muted">
                        {{ date('F j, Y  h:i:s A', strtotime($user->last_login_at)) ?? 'Not provided' }}
                    </p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Joined:</label>
                    <p class="text-muted">
                        {{ $user->created_at->format('F j, Y  h:i:s A') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Task Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Total Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Pending Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Approved Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Rejected Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card rounded">
                    <div class="card-header">
                        <h4 class="card-title">Task Statistics</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Total Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Pending Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Approved Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Rejected Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="card-title">Reviewed Tasks</h6>
                            <p class="text-muted">0</p>
                        </div>
                    </div>
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

        // Report User
        $('#reportForm').submit(function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                url: "{{ route('report_user', $user->id) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $(document).find('span.error-text').text('');
                },
                success: function(response) {
                    if (response.status == 400) {
                        $.each(response.error, function(prefix, val) {
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    } else {
                        $('.reportModal').modal('hide');
                        $('#reportForm')[0].reset();
                        toastr.success('User reported successfully.');
                    }
                }
            });
        });
    });
</script>
@endsection
