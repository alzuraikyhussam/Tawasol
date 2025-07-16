<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function success($data = [], $message = 'Operation successful', $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    protected function error($message = 'Something went wrong', $code = 500, $errors = [])
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }

    protected function validationError($errors, $message = 'Validation failed', $code = 422)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }

    protected function notFound($message = 'Resource not found', $code = 404)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], $code);
    }
}
