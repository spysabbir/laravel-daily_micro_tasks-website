@extends('layouts.template_master')

@section('title', 'User Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Profile Details</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Profile Photo</th>
                                <th>
                                    <img src="{{ asset('uploads/profile_photo') }}/{{ $user->profile_photo }}" alt="Profile Photo" class="img-thumbnail" width="100">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Full Name</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>{{ $user->username ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $user->date_of_birth ? date('d M, Y', strtotime($user->date_of_birth)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $user->gender ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{ $user->phone ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>{{ $user->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>
                                    @if ($user->status == 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($user->status == 'Inactive')
                                        <span class="badge bg-dark">Inactive</span>
                                    @elseif ($user->status == 'Blocked')
                                        <span class="badge bg-warning">Blocked</span>
                                    @else
                                        <span class="badge bg-danger">Banned</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Bio</td>
                                <td>{{ $user->bio ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Referral Code </td>
                                <td>{{ $user->referral_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Referred By</td>
                                <td>{{ $user->referred_by ? $user->referrer->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Email Verified At</td>
                                <td>{{ $user->email_verified_at ? date('d M, Y  h:i:s A', strtotime($user->email_verified_at)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Last Login At</td>
                                <td>{{ $user->last_login_at ? date('d M, Y  h:i:s A', strtotime($user->last_login_at)) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td>{{ $user->created_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                            <tr>
                                <td>Updated At</td>
                                <td>{{ $user->updated_at->format('d M, Y h:i:s A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Deposit Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Withdraw Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Posted Task Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Posted Task Proof Submit Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Worked Task Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Report Details</h3>
            </div>
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">0</h4>
                        <p class="card-text">Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Status Details</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Duration</th>
                                <th>Created By</th>
                                <th>Created Time</th>
                                <th>Resolved Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($userStatuses as $userStatuse)
                            <tr>
                                <td>
                                    @if ($userStatuse->status == 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif ($userStatuse->status == 'Inactive')
                                        <span class="badge bg-dark">Inactive</span>
                                    @elseif ($userStatuse->status == 'Blocked')
                                        <span class="badge bg-warning">Blocked</span>
                                    @else
                                        <span class="badge bg-danger">Banned</span>
                                    @endif
                                </td>
                                <td>{{ $userStatuse->reason }}</td>
                                <td>{{ $userStatuse->blocked_duration ? $userStatuse->blocked_duration . ' hours' : 'N/A' }}</td>
                                <td>{{ $userStatuse->created_by ? $userStatuse->createdBy->name : 'N/A' }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($userStatuse->created_at)) }}</td>
                                <td>{{ $userStatuse->blocked_resolved ? date('j M, Y  h:i:s A', strtotime($userStatuse->blocked_resolved)) : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="50" class="text-center text-info">No blocked status found!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">User Device Details</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle text-center">
                        <thead>
                            <tr>
                                <th>Ip</th>
                                <th>Device Type</th>
                                <th>Updated Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($userDetails as $userDetail)
                            <tr>
                                <td>{{ $userDetail->ip }}</td>
                                <td>{{ $userDetail->device_type }}</td>
                                <td>{{ date('j M, Y  h:i:s A', strtotime($userDetail->updated_at)) }}</td>
                            </tr>
                            @endforeach
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
    });
</script>
@endsection

