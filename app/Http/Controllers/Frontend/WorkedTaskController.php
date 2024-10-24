<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\Block;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;


class WorkedTaskController extends Controller
{
    public function findTasks(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                // Handle DataTable logic here
                $proofTasks = ProofTask::where('user_id', Auth::id())->pluck('post_task_id')->toArray();
                $blockedUsers = Block::where('blocked_by', Auth::id())->pluck('user_id')->toArray();
                $query = PostTask::where('status', 'Running')
                    ->whereNotIn('id', $proofTasks)
                    ->whereNot('user_id', Auth::id())
                    ->whereNotIn('user_id', $blockedUsers);

                if ($request->category_id) {
                    $query->where('post_tasks.category_id', $request->category_id);
                }
                if ($request->sort_by) {
                    if ($request->sort_by == 'low_to_high') {
                        $query->orderBy('earnings_from_work', 'asc');
                    } else if ($request->sort_by == 'high_to_low') {
                        $query->orderBy('earnings_from_work', 'desc');
                    } else if ($request->sort_by == 'latest') {
                        $query->orderBy('approved_at', 'desc');
                    } else if ($request->sort_by == 'oldest') {
                        $query->orderBy('approved_at', 'asc');
                    }
                }

                $findTasks = $query->orderByRaw("
                    CASE
                        WHEN NOW() <= DATE_ADD(post_tasks.approved_at, INTERVAL post_tasks.boosted_time MINUTE)
                        THEN 0
                        ELSE 1
                    END
                ");

                $findTasks = $query->orderBy('approved_at', 'desc')->get();

                return DataTables::of($findTasks)
                    ->addIndexColumn()
                    ->editColumn('category_name', function ($row) {
                        return '<span class="badge bg-primary">'.$row->category->name.'</span>';
                    })
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->id)).'" title="'.$row->title.'" class="text-success">
                                '.$row->title.'
                            </a>
                        ';
                    })
                    ->editColumn('work_needed', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->work_needed . '">' . $proofSubmitted . '/' . $row->work_needed . '</div>
                        </div>
                        ';
                    })
                    ->editColumn('earnings_from_work', function ($row) {
                        return '<span class="badge bg-success">'.get_site_settings('site_currency_symbol') . ' ' . $row->earnings_from_work.'</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return '<span class="badge bg-dark">'.date('d M Y h:i A', strtotime($row->approved_at)).'</span>';
                    })
                    ->editColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('find_task.details', encrypt($row->id)).'" target="_blank" title="View" class="btn btn-info btn-sm">
                            View
                        </a>
                        ';
                        return $action;
                    })
                    ->rawColumns(['category_name', 'title', 'work_needed', 'earnings_from_work', 'approved_at', 'action'])
                    ->make(true);
            }

            // Return categories for filter
            $categories = PostTask::where('status', 'Running')->groupBy('category_id')->select('category_id')->with('category')->get();
            return view('frontend.find_tasks.index', compact('categories'));
        }
    }

    public function findTaskDetails($id)
    {
        $id = decrypt($id);
        $taskDetails = PostTask::findOrFail($id);
        $taskProofExists = ProofTask::where('post_task_id', $id)->where('user_id', Auth::id())->exists();
        $proofCount = ProofTask::where('post_task_id', $id)->count();
        $blocked = Block::where('user_id', $taskDetails->user_id)->where('blocked_by', Auth::id())->exists();
        return view('frontend.find_tasks.view', compact('taskDetails', 'taskProofExists', 'proofCount', 'blocked'));
    }

    public function findTaskProofSubmitLimitCheck($id)
    {
        $id = decrypt($id);
        $proofCount = ProofTask::where('post_task_id', $id)->count();
        $workNeeded =  PostTask::findOrFail($id)->work_needed;

        if ($proofCount >= $workNeeded) {
            return response()->json(['canSubmit' => false]);
        }

        return response()->json(['canSubmit' => true]);
    }

    public function findTaskProofSubmit(Request $request, $id)
    {
        $id = decrypt($id);
        $taskDetails = PostTask::findOrFail($id);

        $rules = [
            'proof_answer' => 'required|string|max:5000',
            'proof_photos' => 'required|array|min:' . $taskDetails->extra_screenshots + 1, // Ensure all required photos are uploaded
            'proof_photos.*' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Each photo must be an image
        ];

        $messages = [
            'proof_answer.required' => 'The proof answer is required.',
            'proof_answer.string' => 'The proof answer must be a string.',
            'proof_answer.max' => 'The proof answer may not be greater than 5000 characters.',
            'proof_photos.required' => 'You must upload all required proof photos.',
            'proof_photos.array' => 'The proof photos must be an array.',
            'proof_photos.min' => 'You must upload at least ' . $taskDetails->extra_screenshots + 1 . ' proof photos.',
            'proof_photos.*.required' => 'Each proof photo is required.',
            'proof_photos.*.image' => 'Each proof photo must be an image.',
            'proof_photos.*.mimes' => 'Each proof photo must be a file of type: jpg, jpeg, png.',
            'proof_photos.*.max' => 'Each proof photo may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $proofCount = ProofTask::where('post_task_id', $id)->count();

        if ($proofCount >= $taskDetails->work_needed) {
            $notification = array(
                'message' => 'Sorry, the required number of work have already submitted proof for this task.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }

        $proof_photos = [];
        $manager = new ImageManager(new Driver());
        foreach ($request->file('proof_photos') as $key => $photo) {
            $proof_photo_name = $id . "-" . $request->user()->id . "-proof_photo-".($key+1).".". $photo->getClientOriginalExtension();
            $image = $manager->read($photo);
            $image->toJpeg(80)->save(base_path("public/uploads/task_proof_photo/").$proof_photo_name);
            $proof_photos[] = $proof_photo_name;
        }

        ProofTask::create([
            'post_task_id' => $id,
            'user_id' => $request->user()->id,
            'proof_answer' => $request->proof_answer,
            'proof_photos' => json_encode($proof_photos),
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task proof submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function workedTaskListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Pending');

                $query = $proofTasks->select('proof_tasks.*')->with('postTask');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.created_at', $request->filter_date);
                }

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('earnings_from_work', function ($row) {
                        return get_site_settings('site_currency_symbol') . ' ' . $row->postTask->earnings_from_work;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['title'])
                    ->make(true);
            }
            return view('frontend.worked_task.pending');
        }
    }

    public function workedTaskListApproved(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Approved');
                $query = $proofTasks->select('proof_tasks.*')->with('postTask');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.approved_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.approved_at', '>', now()->subDays(7));

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('earnings_from_work', function ($row) {
                        return  get_site_settings('site_currency_symbol') . ' ' . $row->postTask->earnings_from_work;
                    })
                    ->editColumn('rating', function ($row) {
                        return $row->rating ? $row->rating->rating . ' <i class="fa-solid fa-star text-warning"></i>' : 'Not Rated';
                    })
                    ->editColumn('bonus', function ($row) {
                        return $row->bonus ? $row->bonus->amount . ' ' . get_site_settings('site_currency_symbol') : 'No Bonus';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->approved_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'bonus', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.approved');
        }
    }

    public function approvedWorkedTaskView($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.approved_view', compact('proofTask', 'postTask'));
    }

    public function workedTaskListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Rejected');
                $query = $proofTasks->select('proof_tasks.*');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.rejected_at', $request->filter_date);
                }

                $query->whereDate('proof_tasks.rejected_at', '>', now()->subDays(7));

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('rejected_reason', function ($row) {
                        return $row->rejected_reason;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.rejected');
        }
    }

    public function rejectedWorkedTaskCheck($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.rejected_check', compact('proofTask' , 'postTask'));
    }

    public function rejectedWorkedTaskReviewed(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reviewed_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $reviewedCount = ProofTask::where('user_id', Auth::id())->where('reviewed_at', '!=', null)->whereMonth('reviewed_at', now()->month)->count();

            if ($reviewedCount >= get_default_settings('task_proof_monthly_free_review_time')) {
                if($request->user()->withdraw_balance < get_default_settings('task_proof_additional_review_charge')){
                    return response()->json([
                        'status' => 401,
                        'error' => 'Insufficient balance in your account to review additional task proof.'
                    ]);
                }else{
                    User::where('id', Auth::id())->update([
                        'withdraw_balance' => $request->user()->withdraw_balance - get_default_settings('task_proof_additional_review_charge'),
                    ]);
                }
            }

            $proofTask = ProofTask::findOrFail($id);

            $proofTask->status = 'Reviewed';
            $proofTask->reviewed_reason = $request->reviewed_reason;
            $proofTask->reviewed_at = now();
            $proofTask->save();

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function workedTaskListReviewed(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $proofTasks = ProofTask::where('user_id', Auth::id())->where('status', 'Reviewed');
                $query = $proofTasks->select('proof_tasks.*');

                if ($request->filter_date){
                    $query->whereDate('proof_tasks.reviewed_at', $request->filter_date);
                }

                // $query->whereDate('proof_tasks.reviewed_at', '>', now()->subDays(7));

                $taskList = $query->get();

                return DataTables::of($taskList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('find_task.details', encrypt($row->post_task_id)).'" title="'.$row->postTask->title.'" class="text-info">
                                '.$row->postTask->title.'
                            </a>
                        ';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->editColumn('reviewed_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->reviewed_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'created_at', 'rejected_at', 'reviewed_at', 'action'])
                    ->make(true);
            }
            return view('frontend.worked_task.reviewed');
        }
    }

    public function reviewedWorkedTaskView($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        $postTask = PostTask::findOrFail($proofTask->post_task_id);
        return view('frontend.worked_task.reviewed_check', compact('proofTask' , 'postTask'));
    }
}
