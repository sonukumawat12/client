<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HelpGuide\Entities\Tag;
use Modules\HelpGuide\Http\Resources\TagResource;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tag = tag::get();
        return tagResource::collection($tag);
    }
}
