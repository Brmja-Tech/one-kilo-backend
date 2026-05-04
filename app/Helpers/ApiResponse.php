<?php

namespace App\Helpers;


class ApiResponse
{
    static function sendResponse($code = 200, $message = null, $data = null, $pagination = null)
    {
        $response = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data,
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        return response()->json($response, $code, [], JSON_PRESERVE_ZERO_FRACTION);
    }
}
