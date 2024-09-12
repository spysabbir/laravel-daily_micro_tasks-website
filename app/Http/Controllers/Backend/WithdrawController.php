<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Notifications\WithdrawNotification;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\BonusNotification;

class WithdrawController extends Controller
{
    public function withdrawRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Pending');

            $query->select('withdraws.*')->orderBy('created_at', 'desc');

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Ragular') {
                        $type = '
                        <span class="badge bg-dark">' . $row->type . '</span>
                        ';
                    } else {
                        $type = '
                        <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                    }
                    return $type;
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user_name', 'type', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.withdraw.index');
    }

    public function withdrawRequestShow(string $id)
    {
        $withdraw = Withdraw::where('id', $id)->first();
        return view('backend.withdraw.show', compact('withdraw'));
    }

    public function withdrawRequestStatusChange(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $withdraw = Withdraw::findOrFail($id);
            if($request->status == 'Rejected' && empty($request->rejected_reason)) {
                return response()->json([
                    'status' => 401,
                    'error' => 'The rejected reason field is required.'
                ]);
            }
            $withdraw->update([
                'status' => $request->status,
                'rejected_reason' => $request->rejected_reason,
                'rejected_by' => $request->status == 'Rejected' ? auth()->user()->id : NULL,
                'rejected_at' => $request->status == 'Rejected' ? now() : NULL,
                'approved_by' => $request->status == 'Approved' ? auth()->user()->id : NULL,
                'approved_at' => $request->status == 'Approved' ? now() : NULL,
            ]);

            $user = User::where('id', $withdraw->user_id)->first();
            $referrer = User::where('id', $user->referred_by)->first();

            if ($referrer && $request->status == 'Approved') {
                $referrer->update([
                    'withdraw_balance' => $referrer->withdraw_balance + ($withdraw->amount * get_default_settings('referral_earning_bonus_percentage')) / 100,
                ]);

                $referrerBonus = Bonus::create([
                    'user_id' => $referrer->id,
                    'bonus_by' => $user->id,
                    'type' => 'Referral Earning Bonus',
                    'amount' => ($withdraw->amount * get_default_settings('referral_earning_bonus_percentage')) / 100,
                ]);
                $referrer->notify(new BonusNotification($referrerBonus));
            }

            $user->notify(new WithdrawNotification($withdraw));

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function withdrawRequestRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Rejected');

            $query->select('withdraws.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('rejected_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->rejectedBy->name . '</span>
                        ';
                })
                ->editColumn('rejected_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->rejected_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user_name', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }

        return view('backend.withdraw.index');
    }

    public function withdrawRequestApproved(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::where('status', 'Approved');

            $query->select('withdraws.*')->orderBy('approved_at', 'desc');

            $approvedData = $query->get();

            return DataTables::of($approvedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('type', function ($row) {
                    if ($row->type == 'Ragular') {
                        $type = '
                        <span class="badge bg-dark">' . $row->type . '</span>
                        ';
                    } else {
                        $type = '
                        <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                    }
                    return $type;
                })
                ->editColumn('approved_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>
                        ';
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->rawColumns(['user_name', 'type', 'approved_by', 'approved_at'])
                ->make(true);
        }

        return view('backend.withdraw.approved');
    }

    public function withdrawRequestDelete(string $id)
    {
        $withdraw = Withdraw::findOrFail($id);

        $withdraw->delete();
    }
}
