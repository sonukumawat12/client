<?php

namespace App\Http\Controllers\Chequer;


use Illuminate\Http\Request;
use App\Chequer\DefaultFonts;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DefaultFontsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fonts.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        // try {
            $output = $this->valid($request);
            if($output['success']){
                $font =  DefaultFonts::where([
                    'business_id' => $business_id,
                    'font' => $request->font,
                ])->first();
                if($font)
                {
                    $output = [
                        'success' => 0,
                        'msg' => __('lang_v1.font_exist_succuss'),
                    ]; 
                }
                else
                {
                
                    $font =  DefaultFonts::create([
                        'user_id'=> auth()->user()->id,
                        'business_id'=>$business_id,
                        'font' => $request->font,
                    ]);
                    $output = [
                        'success' => 1,
                        'data' => [
                            'font'=>$font->font,
                        ],
                        'msg' => __('lang_v1.font_add_succuss')
                    ];
                }
            }
            
            
        // } catch (\Exception $e) {
        //     Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

        //     $output = [
        //         'success' => 0,
        //         'msg' => __('messages.something_went_wrong')
        //     ];
       
        
        // }
        return $output;
    }

    /**
     * Display the specified resource.
     */
    public function show(DefaultFonts $defaultFonts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DefaultFonts $defaultFonts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DefaultFonts $defaultFonts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DefaultFonts $defaultFonts)
    {
        //
    }

    /**
     * validate
     *
     **/
    public function valid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'font' => 'required',
            ]);
        if ($validator->passes()) {
            return ['success'=>true];
        }
        return ['success'=>false,'msg'=>$validator->errors()->first()];
    }
}
