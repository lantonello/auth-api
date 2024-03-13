<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;

abstract class Controller
{
    /**
     * Alias to an error Json Response
     */
    protected function error(string $message, $data = null, $status_code = 422)
    {
        return JsonResponse::error($message, $data, $status_code);
    }

    /**
     * Alias to a successfully Json Response
     */
    protected function success(string $message, $data = null)
    {
        return JsonResponse::success($message, $data);
    }
}
