<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Expense::select('expenses.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->expense_category_id) {
                $query->where('expense_category_id', $request->expense_category_id);
            }

            if ($request->expense_date) {
                $query->where('expense_date', $request->expense_date);
            }

            $expenses = $query->get();

            return DataTables::of($expenses)
                ->addIndexColumn()
                ->editColumn('expense_category_name', function ($row) {
                    return $row->expenseCategory->name;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('expense_date', function ($row) {
                    return date('d M, Y', strtotime($row->expense_date));
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Active') {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs statusBtn">Deactive</button>
                        ';
                    } else {
                        $status = '
                        <span class="badge text-white bg-warning">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs statusBtn">Active</button>
                        ';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                        ';
                    return $btn;
                })
                ->rawColumns(['expense_category_name', 'amount', 'expense_date', 'status', 'action'])
                ->make(true);
        }

        $expense_categories = ExpenseCategory::where('status', 'Active')->get();
        return view('backend.expense.index' , compact('expense_categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Expense::create([
                'expense_category_id' => $request->expense_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function show(string $id)
    {
        $expense = Expense::withTrashed()->where('id', $id)->first();
        return view('backend.expense.show', compact('expense'));
    }

    public function edit(string $id)
    {
        $expense = Expense::where('id', $id)->first();
        return response()->json($expense);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            Expense::where('id', $id)->update([
                'expense_category_id' => $request->expense_category_id,
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->deleted_by = auth()->user()->id;
        $expense->save();
        
        $expense->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Expense::onlyTrashed();

            $trashExpenses = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashExpenses)
                ->addIndexColumn()
                ->editColumn('expense_category_name', function ($row) {
                    return $row->expenseCategory->name;
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->amount . '</span>';
                })
                ->editColumn('expense_date', function ($row) {
                    return date('d M, Y', strtotime($row->expense_date));
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-info btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['expense_category_name', 'amount', 'expense_date', 'action'])
                ->make(true);
        }

        return view('backend.expense.index');
    }

    public function restore(string $id)
    {
        Expense::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Expense::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $expense = Expense::onlyTrashed()->where('id', $id)->first();
        $expense->forceDelete();
    }

    public function status(string $id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->status == "Active") {
            $expense->status = "Inactive";
        } else {
            $expense->status = "Active";
        }

        $expense->updated_by = auth()->user()->id;
        $expense->save();
    }
}