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
                <td>User Name</td>
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
