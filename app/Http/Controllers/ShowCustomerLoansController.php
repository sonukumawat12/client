<?php

namespace App\Http\Controllers;

use App\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;


class ShowCustomerLoansController extends Controller
{
    // Display a listing of the resource.

    public function getLoanData()
    {
        if (request()->ajax()) {
            $loans = Transaction::with(['contact' => function ($query) {
                $query->select('id', 'name');
            }])->select([
                'id', 'contact_id', 'transaction_date', 'ref_no', 'final_total', 'is_settlement',  'transaction_note', 'approved_user'
            ])->where('type', 'direct_customer_loan')->get();

            return Datatables::of($loans)
                ->addColumn('action', function ($loan) {
                    $action = '';
                    if (auth()->user()->can('edit_customer_loans')) {
                        $action .= '<a href="' . action('ShowCustomerLoansController@edit', [$loan->id]) . '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a>';
                    }
                    if (auth()->user()->can('delete_customer_loans')) {
                        $action .= '&nbsp;<button data-href="' . action('ShowCustomerLoansController@destroy', [$loan->id]) . '" class="btn btn-xs btn-danger delete_customer_loan_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                    }
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        };
    }


    public function index()
    {
        return view('show_customer_loans.index');
    }

    // Show the form for editing the specified resource.
    public function edit($id)
    {
        if (!auth()->user()->can('edit_customer_loans')) {
            abort(403, 'Unauthorized action.');
        }
        $loan = Transaction::where('id', $id)->first();
       
        return view('show_customer_loans.edit', ['loan' => $loan]);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $id)
    {

        if (!auth()->user()->can('edit_customer_loans')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'final_total' => 'required|numeric',
            'approved_user' => 'required|string|max:255',
            'transaction_note' => 'nullable|string',
        ]);

        $loan = Transaction::findOrFail($id);

        $loan->final_total = $request->input('final_total');
        $loan->approved_user = $request->input('approved_user');
        $loan->transaction_note = $request->input('transaction_note');

        $loan->save();

        return redirect()->route('show_customer_loans.edit', $id)->with([
            'success' => 1,
            'msg' => 'Loan updated successfully.'
        ]);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        if (!auth()->user()->can('delete_customer_loans')) {
            abort(403, 'Unauthorized action.');
        }

        $loan = Transaction::findOrFail($id);
        $loan->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => 1,
                'msg' => 'Loan deleted successfully.'
            ]);
        }

        return redirect()->route('show_customer_loans.index')->with([
            'success' => 1,
            'msg' => 'Loan deleted successfully.'
        ]);
    }
}
