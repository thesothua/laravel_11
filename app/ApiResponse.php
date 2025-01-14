<?php

namespace App;

trait ApiResponse
{
    /**
     * Send a success response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse(string $message, $data = [], int $statusCode = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Send a response for resource creation.
     *
     * @param string $message
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function createdResponse(string $message, $data = [])
    {
        return $this->successResponse($message, $data, 201);
    }

    /**
     * Send a response for resource deletion.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletedResponse(string $message)
    {
        return $this->successResponse($message, null, 200);
    }

    // =================================================ERROR RESPONSE======================================

    /**
     * Send a general error response.
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse(string $message, int $statusCode = 400, array $errors = null)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Send a validation error response.
     *
     * @param string $message
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationErrorResponse(string $message, array $errors)
    {
        return $this->errorResponse($message, 422, $errors);
    }

    /**
     * Send a not found error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFoundResponse(string $message)
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Send an unauthorized error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorizedResponse(string $message)
    {
        return $this->errorResponse($message, 401);
    }

    /**
     * Send a forbidden error response.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function forbiddenResponse(string $message)
    {
        return $this->errorResponse($message, 403);
    }
}
