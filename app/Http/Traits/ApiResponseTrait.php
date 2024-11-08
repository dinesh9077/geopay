<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;

trait ApiResponseTrait
{
    protected function successResponse($message = 'success', $slug = 'data',  $data = [], $statusCode = 200)
    {
        // Initialize the response structure
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Encrypt the data if it's not empty
        $response['response'] = $this->encryptData($data);

        return response()->json($response, $statusCode);
    }

    protected function errorResponse($message, $statusCode = 500)
    {
        return response()->json(['success' => false, 'message' => $message], $statusCode);
    }

    protected function validateResponse($data, $statusCode = 500)
    {
        return response()->json(['success' => false, 'errors' => $data], $statusCode);
    }

    // Helper function to handle data encryption
    private function encryptData($data)
    {
        $key = base64_decode(env('ENCRYPTION_SECRET_KEY'));

        // Encrypt the data and return it in base64
        $encrypted = openssl_encrypt(
            json_encode($data),
            'AES-256-ECB',
            $key,
            OPENSSL_RAW_DATA
        );

        return base64_encode($encrypted);
    }
}
?>

