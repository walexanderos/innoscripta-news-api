<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Send a successful response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function message_success($data = null, string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send an error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     * @return JsonResponse
     */
    public function message_error(string $message, int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
