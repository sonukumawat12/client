<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Entities\SavedReply;
use Modules\HelpGuide\Http\Resources\SavedReply as SavedReplyResource;
use Illuminate\Support\Facades\Auth;

class SavedRepliesController extends Controller
{
    public function index()
    {
        return view('helpguide::dashboard.saved_replies.index');
    }

    public function show($id)
    {
        $SavedReply = SavedReply::findOrFail($id);
        return new SavedReplyResource($SavedReply);
    }
    
    public function fetch()
    {
        $savedReply = SavedReply::orderBy('id', 'desc')->paginate(500);
        return SavedReplyResource::collection($savedReply);
    }

    public function store(Request $request)
    {
        $savedReply = $request->isMethod('put') ? SavedReply::findOrFail($request->SavedReply_id) : new SavedReply;
        $savedReply->user_id = Auth::id();
        $savedReply->title = $request->input('title');
        $savedReply->content = $request->input('content');

        if ($savedReply->save()) {return new SavedReplyResource($savedReply);}
    }

    public function destroy(Request $request)
    {
        $savedReply = SavedReply::findOrFail($request->SavedReply_id);
        // $this->authorize('delete', $savedReply);
        if ($savedReply->delete()) {return new SavedReplyResource($savedReply);}
    }
}
