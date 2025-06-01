<?php

namespace Modules\Loan\Http\Controllers;

use Modules\Accounting\Services\FlashService;
use Modules\Accounting\Entities\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanFile;

class LoanFileController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        return view('loan::loan_file.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);

        $business_id = session('business.id');

        $loan = Loan::findOrFail($id);

        Media::uploadMedia($business_id, $loan, $request, 'file', false);

        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Create Loan File');

        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $id . '/show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $loan_file = LoanFile::find($id);
        return view('loan::loan_file.edit', compact('loan_file'));
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
            'file' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ods,csv'],
        ]);
        $loan_file = LoanFile::find($id);
        $loan_file->name = $request->name;
        $loan_file->description = $request->description;
        if ($request->hasFile('file')) {
            $file_name = $request->file('file')->store('public/uploads/loans');
            if ($loan_file->link) {
                Storage::delete('public/uploads/loans/' . $loan_file->link);
            }
            $loan_file->link = basename($file_name);
        }
        $loan_file->save();
        activity()->on($loan_file)
            ->withProperties(['id' => $loan_file->id])
            ->log('Update Loan File');
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan_file->loan_id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_file = LoanFile::find($id);
        if ($loan_file->link) {
            Storage::delete('public/uploads/loans/' . $loan_file->link);
        }
        $loan_file->delete();
        activity()->on($loan_file)
            ->withProperties(['id' => $loan_file->id])
            ->log('Delete Loan File');
        // (new FlashService())->onDelete();
        return redirect()->back();
    }
}
