<?php

use App\Models\Newsletter;
use Illuminate\Support\Facades\Schedule;
use App\Mail\NewsletterMail;
use App\Models\ProofTask;
use App\Models\PostTask;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserStatusNotification;
use Carbon\Carbon;

// Send newsletters to subscribers and users
Schedule::call(function () {
    $now = now();
    $newsletters = Newsletter::where('status', 'Draft')
        ->where('sent_at', '<=', $now)
        ->get();

    foreach ($newsletters as $newsletter) {
        $newsletter->update(['status' => 'Sent']);

        $recipients = $newsletter->mail_type == 'Subscriber'
            ? Subscriber::where('status', 'Active')->pluck('email')
            : User::where('status', 'Active')->pluck('email');

        if ($recipients->isNotEmpty()) {
            Mail::to($recipients)->queue(new NewsletterMail($newsletter));
        }
    }
})->everyMinute();

// Unblock users after blocked duration
Schedule::call(function () {
    $now = now();
    $userStatuses = UserStatus::where('status', 'Blocked')
        ->where('blocked_resolved', null)
        ->get();

    foreach ($userStatuses as $userStatus) {

        $activeTime = Carbon::parse($userStatus->created_at)->addHours($userStatus->blocked_duration);

        if ($now->isSameMinute($activeTime)) {
            $userStatus->update(['blocked_resolved' => $now]);
            
            $user = User::find($userStatus->user_id);
            if ($user) {
                $user->update(['status' => 'Active']);
                $user->notify(new UserStatusNotification([
                    'status' => 'Active',
                    'reason' => 'Your account has been unblocked successfully!',
                    'blocked_duration' => null,
                    'created_at' => $now,
                ]));
            }
        }
    }
})->everyMinute();

// Task proof status update to Approved
Schedule::call(function () {
    $now = now();
    $autoApproveTimeInHours = get_default_settings('task_proof_status_auto_approved_time');
    $proofTasks = ProofTask::where('status', 'Pending')->get();

    foreach ($proofTasks as $proofTask) {
        $approvalTime = Carbon::parse($proofTask->created_at)->addHours($autoApproveTimeInHours);

        if ($now->isSameMinute($approvalTime)) {
            $proofTask->update([
                'status' => 'Approved',
                'approved_at' => $now,
                'approved_by' => 1,
            ]);

            $postTask = PostTask::find($proofTask->post_task_id);
            if ($postTask) {
                User::where('id', $proofTask->user_id)->increment('withdraw_balance', $postTask->earnings_from_work);
            }
        }
    }
})->everyMinute();

// Task proof status Rejected refund to task owner
Schedule::call(function () {
    $now = now();
    $autoRefundTimeInHours = get_default_settings('task_proof_status_rejected_charge_auto_refund_time');
    $proofTasks = ProofTask::where('status', 'Rejected')->where('reviewed_at', null)->get();

    foreach ($proofTasks as $proofTask) {
        $refundTime = Carbon::parse($proofTask->rejected_at)->addHours($autoRefundTimeInHours);

        if ($now->isSameMinute($refundTime)) {
            $postTask = PostTask::find($proofTask->post_task_id);
            if ($postTask) {
                $refundAmount = ($postTask->total_charge / $postTask->work_needed);
                User::where('id', $postTask->user_id)->increment('deposit_balance', $refundAmount);
            }
        }
    }
})->everyMinute();
