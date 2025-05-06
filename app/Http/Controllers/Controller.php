<?php

namespace App\Http\Controllers;

abstract class Controller
{
      /**
     * @param     $data
     * @param int $status_code
     */
    public function success($data, int $status_code = 200)
    {
        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ], $status_code);
    }

    /**
     * @param string $message
     * @param int    $status_code
     * @param string $status
     */
    public function fail(string $message, int $status_code = 400, string $status = 'fail')
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
        ], $status_code);
    }
}
