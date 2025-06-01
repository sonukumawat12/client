<?php

namespace Modules\HelpGuide\Http\Controllers;

use Storage;
use Illuminate\Http\File;
use Illuminate\Http\Request;

use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Filesystem\Filesystem;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * @group Upload
 * @authenticated
 * 
 * Upload files to platform
 */
class UploadController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * Upload file to the given storage
     * @urlParam json response type
     * @bodyParam file file required the binary file
     * @urlParam type required the upload type avatar, ticket attachment
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(isDemo()) return Response::json(['status' => 'fail', "message" => 'Upload has been disabled on Demo version' ], 403);

        switch($request->input('type')){
            case "avatar":
                return $this->avatar($request);
                break;
            case "ticket_attachment":
                return $this->ticketAttachment($request);
            case "ticket_conversation":
                return $this->ticketConversation($request);
                break;
            case "ticket":
                return $this->ticket($request);
                break;
            case "article":
                return $this->article($request);
                break;
            case "update":
                return $this->update($request);
                break;
            case "setting":
                return $this->setting($request);
                break;
            case "module":
                return $this->module($request);
                break;
            default:
                return Response::json(['status' => 'fail', "messages" => [__('Invalid type!')]], 422);
        }
    }

    private function avatar($request)
    {
        $rules['file'] = ['required','max:1024','mimes:jpeg,png'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        // Do upload
        $path = $request->file('file')->store("images/avatars", 'public');

        $vImage = Image::make(Storage::disk('public')->path($path));
        if($vImage && $vImage->width() > 1080 ){
            $vImage->resize(1080,null, function ($constraint) {$constraint->aspectRatio();})->save();
        }
        $vImage->resize(300,null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->save(Storage::disk('public')->path(str_replace("images/avatars","images/avatars/thumbs",$path)));
        
        return ['status' => 'ok', "data" => "storage/".$path];
    }

    private function article($request)
    {
        abort_unless(Auth::user()->can('manage_articles'), 403);

        $rules['file'] = ['required','max:1024','mimes:jpeg,jpg,png'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        // Do upload
        $file = $request->file('file');

        $vImage = Image::make($file->path());
        if($vImage && $vImage->width() > 1080 ){
            $vImage = $vImage->resize(1080,null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // $imagePath = 'images/'.$file->hashName();
        // Storage::disk("article")->put($imagePath, $vImage->encode($file->getClientOriginalExtension(), 90));
        
        // return ['status' => 'ok', "data" => Storage::disk("article")->url($imagePath)]; 

        $imageDirectory = public_path('uploads/articles/images');
        if (!file_exists($imageDirectory)) {
            mkdir($imageDirectory, 0755, true); // Ensure the directory exists
        }

        $imagePath = $imageDirectory . '/' . $file->hashName();
        $vImage->encode($file->getClientOriginalExtension(), 90)->save($imagePath);

        return ['status' => 'ok', "data" => '../../public/' . 'uploads/articles/images/' . $file->hashName()];
    }

    private function ticket($request)
    {

        $rules['file'] = ['required','max:1024','mimes:jpeg,jpg,png'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        // Do upload
        $file = $request->file('file');

        $vImage = Image::make($file->path());
        if($vImage && $vImage->width() > 1080 ){
            $vImage = $vImage->resize(1080,null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $imagePath = 'images/'.$file->hashName();
        Storage::disk("ticket")->put($imagePath, $vImage->encode($file->getClientOriginalExtension(), 90));
        return ['status' => 'ok', "data" => Storage::disk("ticket")->url($imagePath)]; 
    }

    private function ticketAttachment($request)
    {
        $rules['file'] = ['required', 'max:1024','mimes:jpeg,jpg,png,pdf,doc,docx'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return  Response::json(['status' => 'fail', "messages" => $validatedData->errors()], 422);
        }

        $file = $request->file('file');
        $uploadedFile = Storage::disk("ticket")->put('attachments', new File( $file->path() ));

        return ['status' => 'ok', "data" => ['path' => 'attachments/'.basename($uploadedFile)]]; 
    }

    private function ticketConversation($request)
    {
        $rules['file'] = ['required', 'max:20000','mimes:jpeg,jpg,png,pdf,doc,docx,zip'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return  Response::json(['status' => 'fail', "messages" => $validatedData->errors()], 422);
        }

        $file = $request->file('file');
        $uploadedFile = Storage::disk("ticket_conversation")->put('attachments', new File( $file->path() ));

        return ['status' => 'ok', "data" => ['path' => 'attachments/'.basename($uploadedFile)]]; 
    }

    private function update($request)
    {
        abort_unless(Auth::user()->can('update_application'), 403);

        $rules['file'] = ['required', 'max:200000','mimes:zip'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return  Response::json(['status' => 'fail', "message" => $validatedData->errors()], 422);
        }

        // clean update folder 
        $filesystem = new \Illuminate\Filesystem\Filesystem;
        $filesystem->cleanDirectory( storage_path('/app/update') );

        $path = $request->file('file')->store("update", 'local');

        return  Response::json(['status' => 'ok', "data" => $path], 200);
    }

    private function module($request)
    {
        abort_unless(Auth::user()->can('upload_module'), 403, __('You are not allowed to upload modules'));

        $rules['file'] = ['required', 'max:200000','mimes:zip'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return  Response::json(['status' => 'fail', "message" => $validatedData->errors()], 422);
        }
     
        $path = $request->file('file')->store("modules", 'local');

        $zip = new \ZipArchive;

        $res = $zip->open( Storage::disk('local')->path($path) );

        if (!$res )  return ['status' => 'fail', 'message' => __('Invalide module file')];

        // Tmp folder
        $tmpf = '/app/modules/extracted/';

        // Clear module folder
        $filesystem = new Filesystem;
        $filesystem->cleanDirectory( storage_path($tmpf) );

        $zip->extractTo( storage_path($tmpf) );

        // get module folder name
        $moduleFolder = array_slice(scandir(storage_path($tmpf)), 2);

        if(count($moduleFolder) == 0 ){
            return ['status' => 'fail', 'message' => __('Invalide module file')];
        }

        // check if valid module
        if(!file_exists( storage_path($tmpf.$moduleFolder[0].'/module.json') )){
            return ['status' => 'fail', 'message' => __('Invalide module file')];
        }

        $zip->extractTo( base_path('Modules') );
        $zip->close();

        // Clean tmp files
        $filesystem->cleanDirectory( storage_path($tmpf) );

        return  Response::json(['status' => 'ok', "message" => "Module has been uploaded"], 200);
    }

    private function setting($request)
    {

        $rules['file'] = ['required','max:1024','mimes:jpeg,jpg,png'];
        $rulesMsg = [];

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "messages" => $validatedData->errors()];
        }

        // Do upload
        $path = $request->file('file')->store("resources", 'public');

        $vImage = Image::make(Storage::disk('public')->path($path));

        return ['status' => 'ok', "url" => asset(Storage::url($path)) ]; 
    }
}
