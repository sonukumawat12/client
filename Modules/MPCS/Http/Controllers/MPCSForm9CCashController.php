<?php

namespace Modules\MPCS\Http\Controllers;

use Modules\MPCS\Entities\MPCSForm9CCash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MPCSForm9CCashController extends Controller
{
    //
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        } 
        $data = $request->validate([
            'date_added' => 'required',
            'form_starting_number' => 'required',
            'previous_sheet_amount' => 'required',
        ]);

        MPCSForm9CCash::create([
            'name' => auth()->user()->username,
            'user_id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'date_added' => $data['date_added'],
            'from_starting_number' => $data['form_starting_number'],
            'previous_sheet_amount' => $data['previous_sheet_amount'],
        ]);

        return redirect()->back();
    }
}
