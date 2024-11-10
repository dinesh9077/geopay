<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;

trait WebResponseTrait
{
    protected function successResponse($message = 'success', $data = [])
    {
        // Initialize the response structure
        $response = [
            'status' => 'success',
            'message' => $message,
        ];

        // Encrypt the data if it's not empty
        $response['response'] = $this->encryptData($data);

        return response()->json($response);
    }

    protected function errorResponse($message)
    {
        return response()->json(['status' => 'error', 'message' => $message]);
    }

    protected function validateResponse($data)
    {
        return response()->json(['status' => 'validation', 'errors' => $data]);
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

