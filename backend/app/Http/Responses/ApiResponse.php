<?php // app/Http/Responses/ApiResponse.php
namespace App\Http\Responses;

class ApiResponse {
    public static function success($data = null, $message = 'Operation successful', $code = 200)
    {
       // dd($data);
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public static function error($message = 'Operation failed', $errors = null, $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
          //  'errors' => $errors
        ], $code);
    }
}
