<?php

namespace Modules\Translation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Modules\Translation\Drivers\Translation;
use Modules\Translation\Http\Requests\LanguageRequest;

class LanguageController extends Controller
{
    private $translation;

    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }

    public function index(Request $request)
    {
        return view('translation::languages.index');
    }

    public function list(Request $request)
    {
        return availableLanguages();
    }

    public function create()
    {
        return view('translation::languages.create');
    }

    public function store(LanguageRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|size:2'
        ]);

        if ($validator->fails()) {
            return Response::json([
                'messages' =>$validator->messages()->all()
            ], 422);
        }

        $this->translation->addLanguage($request->locale, $request->name);

        return Response::json([
            'message' => __('translation::translation.language_added')
        ]);
    }
    
    public function delete(Request $request, $language)
    {
        if( ! array_key_exists($language, availableLanguages()) ){
            return Response::json([
                'message' => __('Language not defined')
            ], 422);
        }

        File::ensureDirectoryExists(storage_path('delete_language'));

        $langFolder = uniqid('deleted_lang_');

        File::ensureDirectoryExists(storage_path('delete_language/'. $langFolder));

        $lfile = resource_path('lang/'.$language.'.json');
        $ffile = resource_path('lang/'.$language);

        if ( File::exists($lfile) ){
            File::move($lfile, storage_path('delete_language/'.$langFolder.'/'.$language));
        }

        if ( File::exists($ffile) ){
            File::move($ffile, storage_path('delete_language/'.$langFolder.'/'.$language.'.json'));
        }

        return Response::json([
            'message' => __('Language has been delete')
        ], 202);
    }
}
