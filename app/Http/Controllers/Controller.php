<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Respuesta estándar de la API.
     *
     * @param  mixed  $data
     */
    protected function apiResponse(
        bool $success,
        string $message,
        mixed $data = null,
        int $status = 200,
        array $errors = []
    ): \Illuminate\Http\JsonResponse {
        $body = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        if (! empty($errors)) {
            $body['errors'] = $errors;
        }

        return response()->json($body, $status);
    }
}
