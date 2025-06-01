<?php

namespace Modules\Customizer\Http\Controllers;

use Modules\HelpGuide\Entities\Setting;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Customizer\Entities\DynamicCSS;
use Illuminate\Contracts\Support\Renderable;

class CustomizerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('customizer::index');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $fields = (array)$request->input('fields');

        foreach ($fields as $key => $value) {
            Setting::add($key, $value, 'string');
        }

        // Generate new style.css file
        DynamicCSS::generateFrontEndCSS();

        return [
            'status' => 'ok',
            'message' => __('Changes has been saved'),
        ];

    }

    public function fetch()
    {
        return Setting::getAllSettings()->pluck('val','name');
    }

}
