<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deposit;
use App\Models\Withdraw;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function depositReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = Deposit::select(
                DB::raw('DATE(deposits.created_at) as deposit_date'),
                DB::raw('SUM(CASE WHEN deposits.method = "Bkash" THEN deposits.amount ELSE 0 END) as bkash_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Nagad" THEN deposits.amount ELSE 0 END) as nagad_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Rocket" THEN deposits.amount ELSE 0 END) as rocket_amount'),
                DB::raw('SUM(CASE WHEN deposits.method = "Withdrawal Balance" THEN deposits.amount ELSE 0 END) as withdrawal_balance_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" THEN deposits.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" THEN deposits.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" THEN deposits.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(deposits.payable_amount) as total_payable_amount'),
                DB::raw('(SUM(deposits.amount) - SUM(deposits.payable_amount)) as deposit_charge'),
                DB::raw('SUM(deposits.amount) as total_amount'),
            );

            // Apply filters based on the request input
            if ($request->method) {
                $query->where('deposits.method', $request->method);
            }

            if ($request->status) {
                $query->where('deposits.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                // Only start_date is selected
                $query->whereDate('deposits.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                // Only end_date is selected
                $query->whereDate('deposits.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                // Both start_date and end_date are selected
                $query->whereBetween(DB::raw('DATE(deposits.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(deposits.created_at)'));
            $query->orderBy('deposit_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Deposit::select(
                DB::raw('SUM(CASE WHEN deposits.method = "Bkash" THEN deposits.amount ELSE 0 END) as total_bkash_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Nagad" THEN deposits.amount ELSE 0 END) as total_nagad_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Rocket" THEN deposits.amount ELSE 0 END) as total_rocket_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.method = "Withdrawal Balance" THEN deposits.amount ELSE 0 END) as total_withdrawal_balance_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Pending" THEN deposits.amount ELSE 0 END) as total_pending_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Approved" THEN deposits.amount ELSE 0 END) as total_approved_amount_sum'),
                DB::raw('SUM(CASE WHEN deposits.status = "Rejected" THEN deposits.amount ELSE 0 END) as total_rejected_amount_sum'),
                DB::raw('SUM(deposits.payable_amount) as total_payable_amount_sum'),
                DB::raw('(SUM(deposits.amount) - SUM(deposits.payable_amount)) as deposit_charge_sum'),
                DB::raw('SUM(deposits.amount) as total_amount_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->method) {
                $totalsQuery->where('deposits.method', $request->method);
            }

            if ($request->status) {
                $totalsQuery->where('deposits.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('deposits.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('deposits.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(deposits.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('deposit_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->deposit_date)) . '</span>';
                })
                ->editColumn('bkash_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->bkash_amount . '</span>';
                })
                ->editColumn('nagad_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->nagad_amount . '</span>';
                })
                ->editColumn('rocket_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->rocket_amount . '</span>';
                })
                ->editColumn('withdrawal_balance_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->withdrawal_balance_amount . '</span>';
                })
                ->editColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . get_site_settings('site_currency_symbol') . ' ' . $row->pending_amount . '</span>';
                })
                ->editColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . get_site_settings('site_currency_symbol') . ' ' . $row->approved_amount . '</span>';
                })
                ->editColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . get_site_settings('site_currency_symbol') . ' ' . $row->rejected_amount . '</span>';
                })
                ->editColumn('total_payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_payable_amount . '</span>';
                })
                ->editColumn('deposit_charge', function ($row) {
                    return '<span class="badge bg-info">' . get_site_settings('site_currency_symbol') . ' ' . $row->deposit_charge . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_bkash_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_bkash_amount_sum,
                    'total_nagad_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_nagad_amount_sum,
                    'total_rocket_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rocket_amount_sum,
                    'total_withdrawal_balance_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_withdrawal_balance_amount_sum,
                    'total_pending_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending_amount_sum,
                    'total_approved_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved_amount_sum,
                    'total_rejected_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected_amount_sum,
                    'total_payable_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_payable_amount_sum,
                    'deposit_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->deposit_charge_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['deposit_date', 'bkash_amount', 'nagad_amount', 'rocket_amount', 'withdrawal_balance_amount', 'pending_amount', 'approved_amount', 'rejected_amount', 'total_payable_amount', 'deposit_charge', 'total_amount'])
                ->make(true);
        }

        return view('backend.report.deposit');
    }

    public function withdrawReport(Request $request)
    {
        if ($request->ajax()) {
            // Query to get individual row data
            $query = Withdraw::select(
                DB::raw('DATE(withdraws.created_at) as withdraw_date'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Bkash" THEN withdraws.amount ELSE 0 END) as bkash_amount'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Nagad" THEN withdraws.amount ELSE 0 END) as nagad_amount'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Rocket" THEN withdraws.amount ELSE 0 END) as rocket_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as pending_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as approved_amount'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as rejected_amount'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Ragular" THEN withdraws.amount ELSE 0 END) as ragular_amount'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Instant" THEN withdraws.amount ELSE 0 END) as instant_amount'),
                DB::raw('SUM(withdraws.payable_amount) as total_payable_amount'),
                DB::raw('(SUM(withdraws.amount) - SUM(withdraws.payable_amount)) as withdraw_charge'),
                DB::raw('SUM(withdraws.amount) as total_amount'),
            );

            // Apply filters based on the request input
            if ($request->type) {
                $query->where('withdraws.type', $request->type);
            }

            if ($request->method) {
                $query->where('withdraws.method', $request->method);
            }

            if ($request->status) {
                $query->where('withdraws.status', $request->status);
            }

            // Date filter: handle cases where either start_date or end_date is provided
            if ($request->start_date && !$request->end_date) {
                // Only start_date is selected
                $query->whereDate('withdraws.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                // Only end_date is selected
                $query->whereDate('withdraws.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                // Both start_date and end_date are selected
                $query->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$request->start_date, $request->end_date]);
            }

            $query->groupBy(DB::raw('DATE(withdraws.created_at)'));
            $query->orderBy('withdraw_date', 'desc');

            // Retrieve the row-level data
            $pendingRequest = $query->get();

            // Query to calculate the totals across all rows
            $totalsQuery = Withdraw::select(
                DB::raw('SUM(CASE WHEN withdraws.method = "Bkash" THEN withdraws.amount ELSE 0 END) as total_bkash_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Nagad" THEN withdraws.amount ELSE 0 END) as total_nagad_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.method = "Rocket" THEN withdraws.amount ELSE 0 END) as total_rocket_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Pending" THEN withdraws.amount ELSE 0 END) as total_pending_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Approved" THEN withdraws.amount ELSE 0 END) as total_approved_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.status = "Rejected" THEN withdraws.amount ELSE 0 END) as total_rejected_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Ragular" THEN withdraws.amount ELSE 0 END) as total_ragular_amount_sum'),
                DB::raw('SUM(CASE WHEN withdraws.type = "Instant" THEN withdraws.amount ELSE 0 END) as total_instant_amount_sum'),
                DB::raw('SUM(withdraws.payable_amount) as total_payable_amount_sum'),
                DB::raw('(SUM(withdraws.amount) - SUM(withdraws.payable_amount)) as withdraw_charge_sum'),
                DB::raw('SUM(withdraws.amount) as total_amount_sum'),
            );

            // Apply the same filters to the totals query
            if ($request->type) {
                $totalsQuery->where('withdraws.type', $request->type);
            }

            if ($request->method) {
                $totalsQuery->where('withdraws.method', $request->method);
            }

            if ($request->status) {
                $totalsQuery->where('withdraws.status', $request->status);
            }

            // Date filter for totals query
            if ($request->start_date && !$request->end_date) {
                $totalsQuery->whereDate('withdraws.created_at', '>=', $request->start_date);
            } elseif (!$request->start_date && $request->end_date) {
                $totalsQuery->whereDate('withdraws.created_at', '<=', $request->end_date);
            } elseif ($request->start_date && $request->end_date) {
                $totalsQuery->whereBetween(DB::raw('DATE(withdraws.created_at)'), [$request->start_date, $request->end_date]);
            }

            // Get the total values
            $totals = $totalsQuery->first();

            // Return the DataTables response with totals
            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('withdraw_date', function ($row) {
                    return '<span class="badge bg-primary">' . date('l j-F, Y', strtotime($row->withdraw_date)) . '</span>';
                })
                ->editColumn('bkash_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->bkash_amount . '</span>';
                })
                ->editColumn('nagad_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->nagad_amount . '</span>';
                })
                ->editColumn('rocket_amount', function ($row) {
                    return '<span class="badge bg-dark">' . get_site_settings('site_currency_symbol') . ' ' . $row->rocket_amount . '</span>';
                })
                ->editColumn('pending_amount', function ($row) {
                    return '<span class="badge bg-warning">' . get_site_settings('site_currency_symbol') . ' ' . $row->pending_amount . '</span>';
                })
                ->editColumn('approved_amount', function ($row) {
                    return '<span class="badge bg-success">' . get_site_settings('site_currency_symbol') . ' ' . $row->approved_amount . '</span>';
                })
                ->editColumn('rejected_amount', function ($row) {
                    return '<span class="badge bg-danger">' . get_site_settings('site_currency_symbol') . ' ' . $row->rejected_amount . '</span>';
                })
                ->editColumn('ragular_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->ragular_amount . '</span>';
                })
                ->editColumn('instant_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->instant_amount . '</span>';
                })
                ->editColumn('total_payable_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_payable_amount . '</span>';
                })
                ->editColumn('withdraw_charge', function ($row) {
                    return '<span class="badge bg-info">' . get_site_settings('site_currency_symbol') . ' ' . $row->withdraw_charge . '</span>';
                })
                ->editColumn('total_amount', function ($row) {
                    return '<span class="badge bg-primary">' . get_site_settings('site_currency_symbol') . ' ' . $row->total_amount . '</span>';
                })
                ->with([
                    'total_bkash_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_bkash_amount_sum,
                    'total_nagad_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_nagad_amount_sum,
                    'total_rocket_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rocket_amount_sum,
                    'total_pending_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_pending_amount_sum,
                    'total_approved_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_approved_amount_sum,
                    'total_rejected_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_rejected_amount_sum,
                    'total_ragular_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_ragular_amount_sum,
                    'total_instant_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_instant_amount_sum,
                    'total_payable_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_payable_amount_sum,
                    'withdraw_charge_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->withdraw_charge_sum,
                    'total_amount_sum' => get_site_settings('site_currency_symbol') . ' ' . $totals->total_amount_sum,
                ])
                ->rawColumns(['withdraw_date', 'bkash_amount', 'nagad_amount', 'rocket_amount', 'pending_amount', 'approved_amount', 'rejected_amount', 'ragular_amount', 'instant_amount', 'total_payable_amount', 'withdraw_charge', 'total_amount'])
                ->make(true);
        }

        return view('backend.report.withdraw');
    }
}
