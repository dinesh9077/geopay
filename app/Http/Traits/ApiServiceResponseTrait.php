<?php

namespace App\Http\Traits;

trait ApiServiceResponseTrait
{
    /**
     * Build a consistent API response structure
     */
    private function buildResponse(bool $status, string $message, ?string $errorCode = null, $data = null, int $statusCode = 200)
    {
        $response = [
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ];

        // Only include error_code if status is false and errorCode is provided
        if (!$status && $errorCode) {
            $response['error_code'] = $errorCode;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Success Response
     */
    protected function successResponse(string $message = 'Success', $data = null, int $statusCode = 200)
    {
        return $this->buildResponse(true, $message, null, $data, $statusCode);
    }

    /**
     * Error Response
     */
    protected function errorResponse(string $message, ?string $errorCode = null, int $statusCode = 400)
    {
        return $this->buildResponse(false, $message, $errorCode, null, $statusCode);
    }

    /**
     * Validation Error Response
     */
    protected function validateResponse(array $errors, ?string $errorCode = 'VALIDATION_ERROR', int $statusCode = 422)
    {
        return $this->buildResponse(false, 'Validation Failed', $errorCode, $errors, $statusCode);
    }
}
