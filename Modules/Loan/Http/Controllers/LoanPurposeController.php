<?php

namespace Modules\Loan\Http\Controllers;

use Modules\Accounting\Services\FlashService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Loan\Entities\LoanPurpose;
use Yajra\DataTables\Facades\DataTables;

class LoanPurposeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = LoanPurpose::forBusiness()
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return view('loan::loan_purpose.index', compact('data'));
    }

    public function get_purposes()
    {
        $query = LoanPurpose::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('loan.loans.purposes.edit')) {
                $action .= '<li><a href="' . url('contact_loan/purpose/' . $data->id . '/edit') . '" class="">' . trans_choice('accounting::core.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('loan.loans.purposes.destroy')) {
                $action .= '<li><a href="' . url('contact_loan/purpose/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('accounting::core.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('contact_loan/purpose/' . $data->id . '/show') . '">' . $data->id . '</a>';
        })->rawColumns(['id', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('loan::loan_purpose.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $loan_purpose = new LoanPurpose();
        $loan_purpose->name = $request->name;
        $loan_purpose->business_id = session('business.id');
        $loan_purpose->save();
        // activity()->on($loan_purpose)
        //     ->withProperties(['id' => $loan_purpose->id])
        //     ->log('Create Loan Purpose');
        // (new FlashService())->onSave();
        return redirect('contact_loan/purpose');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $loan_purpose = LoanPurpose::find($id);
        return view('loan::loan_purpose.show', compact('loan_purpose'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_purpose = LoanPurpose::find($id);
        return view('loan::loan_purpose.edit', compact('loan_purpose'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $loan_purpose = LoanPurpose::find($id);
        $loan_purpose->name = $request->name;
        $loan_purpose->business_id = session('business.id');
        $loan_purpose->save();
        // activity()->on($loan_purpose)
        //     ->withProperties(['id' => $loan_purpose->id])
        //     ->log('Update Loan Purpose');
        // (new FlashService())->onSave();
        return redirect('contact_loan/purpose');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_purpose = LoanPurpose::find($id);
        $loan_purpose->delete();
        // activity()->on($loan_purpose)
        //     ->withProperties(['id' => $loan_purpose->id])
        //     ->log('Delete Loan Purpose');
        // (new FlashService())->onDelete();
        return redirect()->back();
    }
}
