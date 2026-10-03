<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success', int $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function paginated($paginator, string $message = 'Success')
    {
        return response()->json([
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    protected function error(string $message = 'Error', int $code = 400, mixed $errors = null)
    {
        $payload = ['message' => $message];
        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
