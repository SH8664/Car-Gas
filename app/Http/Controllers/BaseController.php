<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BaseController extends Controller
{
    //
    public static function sendResponse($result , $message)
    {
        $response = [
         'success' => true,
         'data' => $result,
         'message' => $message
        ];
        return response()->json($response , 200);
    }
 
    public static function sendError($error , $errorMessage=[] , $code = 404)
    {
        $response = [
         'success' => false,
         'data' => $error
        ];
        if (!empty($errorMessage)) {
         $response['data'] = $errorMessage;
        }
        return response()->json($response , $code);
    }

    public function sendfile($filename)
    {
        $imagePath = public_path('uploads/' . $filename);

        if (file_exists($imagePath)) {
            $fileContents = file_get_contents($imagePath);

            // Determine the image MIME type (e.g., 'image/jpeg', 'image/png', etc.).
            $mimeType = mime_content_type($imagePath);

            // Set appropriate headers to indicate the image type.
            $headers = [
                'Content-Type' => $mimeType,
            ];

            // Return the image as a response with headers.
            return Response::make($fileContents, 200, $headers);
        } else {
            abort(404);
        }
    }
}