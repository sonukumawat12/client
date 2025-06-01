<?php

namespace Modules\Loan\Http\Controllers;

use Modules\Accounting\Services\FlashService;
use Modules\Accounting\Entities\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Loan\Entities\LoanCollateral;
use Modules\Loan\Entities\LoanCollateralHistory;
use Modules\Loan\Entities\LoanCollateralType;

class LoanCollateralController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        $loan_collateral_types = LoanCollateralType::forBusiness()->get();
        $statuses = LoanCollateral::getStatuses();
        $conditions = LoanCollateral::getConditions();
        return view('loan::loan_collateral.create', compact('loan_collateral_types', 'id', 'statuses', 'conditions'));
    }

    private function upload_collateral_media(Request $request, LoanCollateral $loan_collateral, $file_name)
    {
        $business_id = session('business.id');

        $request->description = $file_name;

        Media::uploadMedia($business_id, $loan_collateral, $request, $file_name, false);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $loan_id)
    {
        $input = $request->validate([
            // required fields
            'loan_collateral_type_id' => ['required'],
            'product_name' => ['required'],
            'registration_date' => ['required'],
            'value' => ['required'],

            //status
            'status' => ['required'],
            'status_change_date' => ['required'],

            //optional fields
            'serial_number' => ['nullable'],
            'model_name' => ['nullable'],
            'model_number' => ['nullable'],
            'color' => ['nullable'],
            'manufacture_date' => ['nullable'],
            'condition' => ['nullable'],
            'address' => ['nullable'],
            'description' => ['nullable'],
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
            'photo' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],

            //for vehicles only
            'registration_number' => ['nullable'],
            'mileage' => ['nullable'],
            'engine_number' => ['nullable'],
        ]);

        try {
            DB::beginTransaction();

            $input['created_by_id'] = Auth::id();

            $input['loan_id'] = $loan_id;

            $loan_collateral = LoanCollateral::create($input);

            LoanCollateralHistory::create([
                'loan_collateral_id' => $loan_collateral->id,
                'updated_by_user_id' => Auth::id(),
                'status' => $request->status,
                'status_change_date' => $request->status_change_date
            ]);

            $this->upload_collateral_media($request, $loan_collateral, 'file');

            $this->upload_collateral_media($request, $loan_collateral, 'photo');

            DB::commit();

            activity()->on($loan_collateral)
                ->withProperties(['id' => $loan_collateral->id])
                ->log('Create Loan Collateral');
        } catch (\Exception $e) {
            DB::rollBack();
            // return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan_id . '/show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_collateral_types = LoanCollateralType::forBusiness()->get();
        $loan_collateral = LoanCollateral::with('history')->findOrFail($id);
        $status_change_dates = $loan_collateral->history->pluck('status_change_date');
        $statuses = LoanCollateral::getStatuses();
        $conditions = LoanCollateral::getConditions();
        return view('loan::loan_collateral.edit', compact('loan_collateral_types', 'loan_collateral', 'statuses', 'conditions', 'status_change_dates'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->validate([
            // required fields
            'loan_collateral_type_id' => ['required'],
            'product_name' => ['required'],
            'registration_date' => ['required'],
            'value' => ['required'],

            //status
            'status' => ['sometimes'],
            'status_change_date' => ['sometimes'],

            //optional fields
            'serial_number' => ['nullable'],
            'model_name' => ['nullable'],
            'model_number' => ['nullable'],
            'color' => ['nullable'],
            'manufacture_date' => ['nullable'],
            'condition' => ['nullable'],
            'address' => ['nullable'],
            'description' => ['nullable'],
            // 'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
            // 'photo' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],

            //for vehicles only
            'registration_number' => ['nullable'],
            'mileage' => ['nullable'],
            'engine_number' => ['nullable'],
        ]);

        try {
            DB::beginTransaction();

            $loan_collateral = LoanCollateral::with('history')->findOrFail($id);

            $input['status'] = $request->status ?: $loan_collateral->status;

            $input['status_change_date'] = $request->status_change_date ?: $loan_collateral->history->pluck('status_change_date')->last();

            $loan_collateral->update($input);

            // Update the loan collateral history if the status changes
            if (!empty($request->status) && $loan_collateral->status != $request->status) {
                LoanCollateralHistory::create([
                    'loan_collateral_id' => $loan_collateral->id,
                    'updated_by_user_id' => Auth::id(),
                    'status' => $request->status,
                    'status_change_date' => $request->status_change_date
                ]);
            }

            DB::commit();

            activity()->on($loan_collateral)
                ->withProperties(['id' => $loan_collateral->id])
                ->log('Update Loan Collateral');
        } catch (\Exception $e) {
            DB::rollBack();
            // return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan_collateral->loan_id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_collateral = LoanCollateral::findOrFail($id);
        if ($loan_collateral->link) {
            Storage::delete('public/uploads/loans/' . $loan_collateral->link);
        }
        $loan_collateral->delete();
        activity()->on($loan_collateral)
            ->withProperties(['id' => $loan_collateral->id])
            ->log('Delete Loan Collateral');
        // (new FlashService())->onDelete();
        return redirect()->back();
    }
}
