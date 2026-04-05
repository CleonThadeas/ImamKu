<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait untuk format response JSON yang konsisten di seluruh API.
 * Format: { "success": bool, "data": mixed, "message": string }
 */
trait ApiResponse
{
    /**
     * Response sukses.
     */
    protected function success(mixed $data = null, string $message = 'Berhasil', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $data,
            'message' => $message,
        ], $code);
    }

    /**
     * Response error.
     */
    protected function error(string $message = 'Terjadi kesalahan', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'data'    => null,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
