<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\PostTask;
use App\Models\ProofTask;
use App\Models\TaskPostCharge;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\Rating;
use App\Models\Bonus;
use App\Models\Report;
use App\Notifications\RatingNotification;
use App\Notifications\BonusNotification;


class PostedTaskController extends Controller
{
    public function postTask()
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            $categories = Category::where('status', 'Active')->get();
            return view('frontend.post_task.create', compact('categories'));
        }
    }

    public function postTaskGetSubCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        $response = [];
        if ($subCategories->isNotEmpty()) {
            $response['sub_categories'] = $subCategories;
        }

        return response()->json($response);
    }

    public function postTaskGetChildCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->get();

        $response = [];
        if ($childCategories->isNotEmpty()) {
            $response['child_categories'] = $childCategories;
        }

        $response['task_post_charge'] = TaskPostCharge::where('category_id', $categoryId)
                                                ->where('sub_category_id', $subCategoryId)
                                                ->first();

        return response()->json($response);
    }

    public function postTaskGetTaskPostCharge(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategoryId = $request->child_category_id;

        $charge = TaskPostCharge::where('category_id', $categoryId)
            ->where('sub_category_id', $subCategoryId)
            ->where('child_category_id', $childCategoryId)
            ->first();

        return response()->json($charge);
    }

    public function postTaskStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof' => 'required|string',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'work_needed' => 'required|numeric|min:1',
            'earnings_from_work' => 'required|numeric|min:1',
            'extra_screenshots' => 'required|numeric|min:0',
            'boosted_time' => 'required|numeric|min:0',
            'running_day' => 'required|numeric|min:1',
        ]);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo". date('YmdHis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/task_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = null;
        }

        $task_post_charge = (($request->work_needed * $request->earnings_from_work) + ($request->extra_screenshots * get_default_settings('task_posting_additional_screenshot_charge'))) + (($request->boosted_time / 15) * get_default_settings('task_posting_boosted_time_charge'));

        $site_charge = $task_post_charge * get_default_settings('task_posting_charge_percentage') / 100;

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - ($task_post_charge + $site_charge),
        ]);

        PostTask::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof' => $request->required_proof,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'work_needed' => $request->work_needed,
            'earnings_from_work' => $request->earnings_from_work,
            'extra_screenshots' => $request->extra_screenshots,
            'boosted_time' => $request->boosted_time,
            'running_day' => $request->running_day,
            'charge' => $task_post_charge,
            'site_charge' => $site_charge,
            'total_charge' => $task_post_charge + $site_charge,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task post submitted successfully.',
            'alert-type' => 'success'
        );

        return to_route('posted_task.list.pending')->with($notification);
    }

    public function postTaskEdit($id)
    {
        $id = decrypt($id);
        $categories = Category::where('status', 'Active')->get();
        $postTask = PostTask::findOrFail($id);
        return view('frontend.post_task.edit', compact('categories', 'postTask'));
    }

    public function postTaskUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof' => 'required|string',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'work_needed' => 'required|numeric|min:1',
            'earnings_from_work' => 'required|numeric|min:1',
            'extra_screenshots' => 'required|numeric|min:0',
            'boosted_time' => 'required|numeric|min:0',
            'running_day' => 'required|numeric|min:1',
        ]);

        $postTask = PostTask::findOrFail($id);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo". date('YmdHis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/task_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = $postTask->thumbnail;
        }

        $task_post_charge = (($request->work_needed * $request->earnings_from_work) + ($request->extra_screenshots * get_default_settings('task_posting_additional_screenshot_charge'))) + (($request->boosted_time / 15) * get_default_settings('task_posting_boosted_time_charge'));

        $site_charge = $task_post_charge * get_default_settings('task_posting_charge_percentage') / 100;

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - ($task_post_charge + $site_charge),
        ]);

        $postTask->update([
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof' => $request->required_proof,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'work_needed' => $request->work_needed,
            'earnings_from_work' => $request->earnings_from_work,
            'extra_screenshots' => $request->extra_screenshots,
            'boosted_time' => $request->boosted_time,
            'running_day' => $request->running_day,
            'charge' => $task_post_charge,
            'site_charge' => $site_charge,
            'total_charge' => $task_post_charge + $site_charge,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Task post updated successfully.',
            'alert-type' => 'success'
        );

        return to_route('posted_task.list.pending')->with($notification);
    }

    public function postedTaskListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Pending');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListPending = $query->get();

                return DataTables::of($taskListPending)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('frontend.posted_task.pending');
        }
    }

    public function postedTaskView($id)
    {
        $postTask = PostTask::findOrFail($id);
        return view('frontend.posted_task.view', compact('postTask'));
    }

    public function postedTaskListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Rejected');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListRejected = $query->get();

                return DataTables::of($taskListRejected)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <a href="' . route('post_task.edit', encrypt($row->id)) . '" class="btn btn-primary btn-xs">Edit</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['created_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.rejected');
        }
    }

    public function postedTaskListCanceled(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Canceled');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListCanceled = $query->get();

                return DataTables::of($taskListCanceled)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->work_needed . '">' . $proofSubmitted . '/' . $row->work_needed . '</div>
                        </div>
                        ';
                    })
                    ->editColumn('proof_status', function ($row) {
                        $statuses = [
                            'Pending' => 'bg-warning',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Reviewed' => 'bg-info'
                        ];
                        $proofStatus = '';
                        $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                        if ($proofCount === 0) {
                            return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                        }
                        foreach ($statuses as $status => $class) {
                            $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                            if ($count > 0) {
                                $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                            }
                        }
                        return $proofStatus;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->editColumn('canceled_at', function ($row) {
                        return $row->canceled_by == auth()->user()->id ? date('d M Y h:i A', strtotime($row->canceled_at)) : 'Canceled by ' . $row->canceledBy->name . ' at ' . date('d M Y h:i A', strtotime($row->canceled_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.canceled');
        }
    }

    public function postedTaskListPaused(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Paused');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListPaused = $query->get();

                return DataTables::of($taskListPaused)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->work_needed . '">' . $proofSubmitted . '/' . $row->work_needed . '</div>
                        </div>
                        ';
                    })
                    ->editColumn('proof_status', function ($row) {
                        $statuses = [
                            'Pending' => 'bg-warning',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Reviewed' => 'bg-info'
                        ];
                        $proofStatus = '';
                        $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                        if ($proofCount === 0) {
                            return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                        }
                        foreach ($statuses as $status => $class) {
                            $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                            if ($count > 0) {
                                $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                            }
                        }
                        return $proofStatus;
                    })
                    ->editColumn('paused_at', function ($row) {
                        return $row->paused_by == auth()->user()->id ? date('d M Y h:i A', strtotime($row->paused_at)) : 'Paused by ' . $row->pausedBy->name . ' at ' . date('d M Y h:i A', strtotime($row->paused_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.paused');
        }
    }

    public function runningPostedTaskCanceled(Request $request, $id)
    {
        $postTask = PostTask::findOrFail($id);

        $proofTasks = ProofTask::where('post_task_id', $id)->whereIn('status', ['Pending', 'Reviewed'])->count();

        if ($proofTasks > 0) {
            return response()->json([
                'status' => 400,
                'error' => 'You can not cancel this task. Because some workers are already submitted proof. If you want to cancel this task, please reject or approve all proof first.'
            ]);
        }

        if ($request->has('check') && $request->check == true) {
            return response()->json([
                'status' => 200,
                'message' => 'Task found. Proceed with cancellation.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => 'Please enter a valid reason.'
            ]);
        } else {
            $user = User::findOrFail($postTask->user_id);
            if ($postTask->status != 'Rejected') {
                $proofTasks = ProofTask::where('post_task_id', $postTask->id)->count();

                $refundAmount = ($postTask->total_charge / $postTask->work_needed) * ($postTask->work_needed - $proofTasks);

                $user->deposit_balance = $user->deposit_balance + $refundAmount;
                $user->save();
            }

            $postTask->status = 'Canceled';
            $postTask->cancellation_reason = $request->message;
            $postTask->canceled_by = auth()->user()->id;
            $postTask->canceled_at = now();
            $postTask->save();

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format($user->deposit_balance, 2, '.', ''),
                'success' => 'Task canceled successfully.'
            ]);
        }
    }

    public function postedTaskListRunning(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Running');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListRunning = $query->get();

                return DataTables::of($taskListRunning)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->work_needed . '">' . $proofSubmitted . '/' . $row->work_needed . '</div>
                        </div>
                        ';
                    })
                    ->editColumn('proof_status', function ($row) {
                        $statuses = [
                            'Pending' => 'bg-warning',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Reviewed' => 'bg-info'
                        ];
                        $proofStatus = '';
                        $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                        if ($proofCount === 0) {
                            return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                        }
                        foreach ($statuses as $status => $class) {
                            $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                            if ($count > 0) {
                                $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                            }
                        }
                        return $proofStatus;
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Update</button>
                            <a href="' . route('proof_task.list', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'approved_at', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.running');
        }
    }

    public function runningPostedTaskEdit(string $id)
    {
        $postTask = PostTask::where('id', $id)->first();
        return response()->json($postTask);
    }

    public function runningPostedTaskUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'work_needed' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $postTask = PostTask::findOrFail($id);

                $task_post_charge = (($request->work_needed * $postTask->earnings_from_work));

                $site_charge = $task_post_charge * get_default_settings('task_posting_charge_percentage') / 100;

                $total_charge = $task_post_charge + $site_charge;

            if ($request->user()->deposit_balance < $total_charge) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Insufficient balance. Please deposit first. Your current balance is ' . $request->user()->deposit_balance . ' ' . get_site_settings('site_currency_symbol') . '.'
                ]);
            } else {

                $request->user()->update([
                    'deposit_balance' => $request->user()->deposit_balance - $total_charge,
                ]);

                PostTask::where('id', $id)->update([
                    'work_needed' => $postTask->work_needed + $request->work_needed,
                    'charge' => $postTask->charge + $task_post_charge,
                    'site_charge' => $postTask->site_charge + $site_charge,
                    'total_charge' => $postTask->total_charge + $total_charge,
                ]);

                return response()->json([
                    'status' => 200,
                    'deposit_balance' => number_format($request->user()->deposit_balance, 2, '.', ''),
                ]);
            }
        }
    }

    public function proofTaskList(Request $request, $id)
    {
        if ($request->ajax()) {
            $query = ProofTask::where('post_task_id', decrypt($id));

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $query->select('proof_tasks.*');

            $proofTasks = $query->get();

            return DataTables::of($proofTasks)
                ->addColumn('checkbox', function ($row) {
                    $checkPending = $row->status != 'Pending' ? 'disabled' : '';
                    $checkbox = '
                        <input type="checkbox" class="form-check-input checkbox" value="' . $row->id . '" ' . $checkPending . '>
                    ';
                    return $checkbox;
                })
                ->editColumn('user', function ($row) {
                    $user = '
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $row->user->userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    } else if ($row->status == 'Approved') {
                        $status = '<span class="badge bg-success">' . $row->status . '</span>';
                    } else if ($row->status == 'Rejected') {
                        $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                    }else {
                        $status = '<span class="badge bg-info">' . $row->status . '</span>';
                    }
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->editColumn('checked_at', function ($row) {
                    if ($row->approved_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->approved_at));
                    } else if ($row->rejected_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->rejected_at));
                    } else if ($row->reviewed_at) {
                        $checked_at = date('d M Y h:i A', strtotime($row->reviewed_at));
                    } else {
                        $checked_at = 'N/A';
                    }
                    return $checked_at;
                })
                ->addColumn('action', function ($row) {
                    if ($row->status == 'Rejected') {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs reportProofTaskBtn" data-bs-toggle="modal" data-bs-target=".reportProofTaskModal">Report</button>';
                    } else {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>';
                    }
                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'user', 'status', 'created_at', 'checked_at', 'action'])
                ->make(true);
        }

        $postTask = PostTask::findOrFail(decrypt($id));
        $proofSubmitted = ProofTask::where('post_task_id', $postTask->id)->get();
        return view('frontend.posted_task.proof_list', compact('postTask', 'proofSubmitted'));
    }

    public function proofTaskReport($id)
    {
        $proofTask = ProofTask::findOrFail($id);

        $reportStatus = Report::where('proof_task_id', $proofTask->id)->where('user_id', $id)->first();

        return response()->json([
            'status' => 200,
            'reportStatus' => $reportStatus,
            'proofTask' => $proofTask
        ]);
    }

    public function proofTaskApprovedAll($id)
    {
        $proofTasks = ProofTask::where('post_task_id', $id)->where('status', 'Pending')->get();

        $postTask = PostTask::findOrFail($id);

        foreach ($proofTasks->pluck('user_id') as $user_id) {
            $user = User::findOrFail($user_id);
            $user->withdraw_balance = $user->withdraw_balance + $postTask->earnings_from_work;
            $user->save();
        }

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Approved';
            $proofTask->approved_at = now();
            $proofTask->approved_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskSelectedItemApproved(Request $request)
    {
        $proofTasks = ProofTask::whereIn('id', $request->id)->get();

        $postTask = PostTask::findOrFail($proofTasks->first()->post_task_id);

        foreach ($proofTasks as $proofTask) {
            $user = User::findOrFail($proofTask->user_id);
            $user->withdraw_balance = $user->withdraw_balance + $postTask->earnings_from_work;
            $user->save();
        }

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Approved';
            $proofTask->approved_at = now();
            $proofTask->approved_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskSelectedItemRejected(Request $request)
    {
        $proofTasks = ProofTask::whereIn('id', $request->id)->get();

        foreach ($proofTasks as $proofTask) {
            $proofTask->status = 'Rejected';
            $proofTask->rejected_reason = $request->message;
            $proofTask->rejected_at = now();
            $proofTask->rejected_by = auth()->user()->id;
            $proofTask->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function proofTaskCheck($id)
    {
        $proofTask = ProofTask::findOrFail($id);
        return view('frontend.posted_task.proof_check', compact('proofTask'));
    }

    public function proofTaskCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Rejected',
            'bonus' => 'nullable|numeric|min:0|max:' . get_default_settings('task_proof_max_bonus_amount'),
            'rating' => 'nullable|numeric|min:0|max:5',
            'rejected_reason' => 'required_if:status,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $proofTask = ProofTask::findOrFail($id);
            $postTask = PostTask::findOrFail($proofTask->post_task_id);
            $user = User::findOrFail($proofTask->user_id);

            if ($request->status == 'Approved') {
                $user->withdraw_balance = $user->withdraw_balance + $postTask->earnings_from_work + $request->bonus;
                $user->save();

                if ($request->rating) {
                    Rating::create([
                        'user_id' => $proofTask->user_id,
                        'rated_by' => auth()->user()->id,
                        'post_task_id' => $postTask->id,
                        'rating' => $request->rating,
                    ]);
                    $rating = Rating::where('user_id', $proofTask->user_id)->where('post_task_id', $postTask->id)->first();
                    $user->notify(new RatingNotification($rating));
                }

                if ($request->bonus) {
                    if ($request->bonus <= auth()->user()->deposit_balance) {
                        auth()->user()->update([
                            'deposit_balance' => auth()->user()->deposit_balance - $request->bonus
                        ]);
                    }else if ($request->bonus <= auth()->user()->withdraw_balance) {
                        auth()->user()->update([
                            'withdraw_balance' => auth()->user()->withdraw_balance - $request->bonus
                        ]);
                    }else{
                        return response()->json([
                            'status' => 401,
                            'error' => 'Insufficient balance. Please deposit first. Your current deposit balance is ' . get_site_settings('site_currency_symbol') . ' ' . auth()->user()->deposit_balance . ' and withdraw balance is ' . get_site_settings('site_currency_symbol') . ' ' . auth()->user()->withdraw_balance . '.'
                        ]);
                    }

                    Bonus::create([
                        'user_id' => $proofTask->user_id,
                        'bonus_by' => auth()->user()->id,
                        'type' => 'Proof Task Approved Bonus',
                        'post_task_id' => $postTask->id,
                        'amount' => $request->bonus,
                    ]);
                    $bonus = Bonus::where('user_id', $proofTask->user_id)->where('post_task_id', $postTask->id)->first();
                    $user->notify(new BonusNotification($bonus));
                }
            }

            $proofTask->status = $request->status;
            $proofTask->rejected_reason = $request->rejected_reason ?? NULL;
            $proofTask->rejected_at = $request->status == 'Rejected' ? now() : NULL;
            $proofTask->rejected_by = $request->status == 'Rejected' ? auth()->user()->id : NULL;
            $proofTask->approved_at = $request->status == 'Approved' ? now() : NULL;
            $proofTask->approved_by = $request->status == 'Approved' ? auth()->user()->id : NULL;
            $proofTask->save();

            return response()->json([
                'status' => 200,
                'deposit_balance' => number_format(auth()->user()->deposit_balance, 2, '.', ''),
                'withdraw_balance' => number_format(auth()->user()->withdraw_balance, 2, '.', ''),
            ]);
        }
    }

    public function runningPostedTaskPausedResume($id)
    {
        $postTask = PostTask::findOrFail($id);

        if ($postTask->status == 'Paused') {
            $postTask->status = 'Running';
        } else if ($postTask->status == 'Running') {
            $postTask->paused_at = now();
            $postTask->paused_by = auth()->user()->id;
            $postTask->status = 'Paused';
        }

        $postTask->save();

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function postedTaskListCompleted(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard');
        } else {
            if ($request->ajax()) {
                $query = PostTask::where('user_id', Auth::id())->where('status', 'Completed');

                $query->select('post_tasks.*')->orderBy('created_at', 'desc');

                $taskListCompleted = $query->get();

                return DataTables::of($taskListCompleted)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = ProofTask::where('post_task_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->work_needed) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->work_needed . '">' . $proofSubmitted . '/' . $row->work_needed . '</div>
                        </div>
                        ';
                    })
                    ->editColumn('proof_status', function ($row) {
                        $statuses = [
                            'Pending' => 'bg-warning',
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Reviewed' => 'bg-info'
                        ];
                        $proofStatus = '';
                        $proofCount = ProofTask::where('post_task_id', $row->id)->count();
                        if ($proofCount === 0) {
                            return '<span class="badge bg-secondary">Proof not submitted yet.</span>';
                        }
                        foreach ($statuses as $status => $class) {
                            $count = ProofTask::where('post_task_id', $row->id)->where('status', $status)->count();
                            if ($count > 0) {
                                $proofStatus .= "<span class=\"badge $class\"> $status: $count</span> ";
                            }
                        }
                        return $proofStatus;
                    })
                    ->editColumn('total_charge', function ($row) {
                        $total_charge = '
                            <span class="badge bg-primary">' . $row->total_charge .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $total_charge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $rejectedProof = ProofTask::where('post_task_id', $row->id)->where('status', 'Rejected')->count();
                        $proofStatus = '
                            <span class="badge bg-success"> Expencese: ' . $row->total_charge - ($row->earnings_from_work * $rejectedProof) .' '. get_site_settings('site_currency_symbol') . '</span>
                            <span class="badge bg-danger"> Return: ' . $row->earnings_from_work * $rejectedProof .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $proofStatus;
                    })
                    ->addColumn('action', function ($row) {
                        $status = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $status;
                    })
                    ->rawColumns(['proof_submitted', 'proof_status', 'total_charge', 'charge_status', 'action'])
                    ->make(true);
            }
            return view('frontend.posted_task.completed');
        }
    }
}
