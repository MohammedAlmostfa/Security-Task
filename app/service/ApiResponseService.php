<?php

namespace App\Service;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as PaginationLengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponseService
{
    /**
     * Return a successful JSON response.
     *
     * @param string $message The success message.
     * @param mixed $data The data to be returned in the response.
     * @param int $status The HTTP status code.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($message = 'Done Successfully!', $data = null, $status = 200)
    {
        return response()->json([
            'message' => trans($message),
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message The error message.
     * @param mixed $data The data to be returned in the response.
     * @param int $status The HTTP status code.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = 'Operation failed!', $data = null, $status = 400)
    {
        return response()->json([
            'message' => trans($message),
            'data' => $data,
        ], $status);
    }

    /**
     * Return a JSON response with a single value.
     *
     * @param string $message The success message.
     * @param mixed $data The data to be returned in the response.
     * @param int $status The HTTP status code.
     * @return \Illuminate\Http\JsonResponse
     */
    public static function Showdata($message = 'Value showing successfully', $data, $status = 200)
    {
        return response()->json([
            'message' => trans($message),
            'data' => $data,
        ], $status);
    }
}
