<?php

namespace App\Http\Services;

class ResponseFormatter
{

    public static function formatResponse(bool $success, $message, $data)
    {
        return [
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ];
    }

}