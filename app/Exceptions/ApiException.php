<?php

namespace App\Exceptions;

use Exception;
use App\Helpers\JsonResponse;

class ApiException extends Exception
{
    protected $data;

    public function __construct(string $message = "", $data = null)
    {
        parent::__construct($message);
        $this->data = $data;
    }

    /**
     * Report or log an exception.
     */
    public function report()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return JsonResponse::error($this->getMessage(), $this->data, 422);
    }
}
