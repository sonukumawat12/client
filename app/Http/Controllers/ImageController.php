<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function show($filename)
    {
        // Construct the full path of the image
        $path = storage_path('app/media/' . $filename);

        // Check if the file exists at the given path
        if (!file_exists($path)) {
            abort(404);
        }
        

        // Get the image data and its MIME type
        $file = file_get_contents($path);
        $type = mime_content_type($path);

        // Return the image data as a response with the correct MIME type
        ob_get_clean();
        return response($file)->header('Content-Type', $type);
    }
}
