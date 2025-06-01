<?php

namespace Modules\Loan\Http\Controllers;

use Modules\Accounting\Services\FlashService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Loan\Entities\LoanNote;

class LoanNoteController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create($id)
    {
        return view('loan::loan_note.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'description' => ['required'],
        ]);
        $loan_note = new LoanNote();
        $loan_note->created_by_id = Auth::id();
        $loan_note->loan_id = $id;
        $loan_note->description = $request->description;
        $loan_note->save();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Create Loan Note');
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
        $loan_note = LoanNote::findOrFail($id);
        return view('loan::loan_note.edit', compact('loan_note'));
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
            'description' => ['required'],
        ]);
        $loan_note = LoanNote::findOrFail($id);
        $loan_note->description = $request->description;
        $loan_note->save();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Update Loan Note');
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan_note->loan_id . '/show');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $loan_note = LoanNote::findOrFail($id);
        $loan_note->delete();
        activity()->on($loan_note)
            ->withProperties(['id' => $loan_note->id])
            ->log('Delete Loan Note');
        // (new FlashService())->onDelete();
        return redirect()->back();
    }
}
