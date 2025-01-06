<?php

namespace App;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param  mixed  $data
     * @param  string $message
     * @param  int    $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, $message = 'Operation Successful', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     *
     * @param  string $message
     * @param  int    $status
     * @param  mixed  $errors
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message, $status = 400, $data = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'  => $data,
        ], $status);
    }
}
