<?php

namespace App\Helpers;

class JsonResponse
{
    /** @var bool Indicates if this is a success (true) or error (false) message. */
    protected $success;
    
    /** @var string Holds the message to be sent. */
    protected $message;
    
    /** @var \stdClass Holds aditional data to be sent. */
    protected $data;

    /** @var integer HTTP Status code. */
    protected $http_status;

    /**
     * Creates a new instance of JsonResponse object.
     */
    public function __construct( bool $success, string $message = null, $data = null )
    {
        $this->success = $success;
        $this->message = $message;
        $this->data    = $data;
        
        if( is_array($data) )
            $this->data = (object) $data;
    }

    /**
     * Returns a response
     */
    public function response($status_code = null)
    {
        if( $this->success )
        {
            $this->http_status = $status_code ?? 200;
            return $this->successResponse();
        }

        $this->http_status = $status_code ?? 422;
        return $this->errorResponse();
    }

    /**
     * Returns a Successfully response
     */
    protected function successResponse()
    {
        return response()->json( $this->getArray(), $this->http_status );
    }

    /**
     * Returns an Error response
     */
    protected function errorResponse()
    {
        return response()->json( $this->getArray(), $this->http_status );
    }

    // STATIC METHODS
    /**
     * Returns a success response with given message and data.
     * @param string $message The message to be returned.
     * @param        $data    Optional data do be returned.
     */
    public static function success( string $message, $data = null )
    {
        $instance = new JsonResponse(true, $message, $data);
        $instance->http_status = 200;

        return $instance->successResponse();
    }

    /**
     * Returns an error response with given message and data;
     * @param string $message The message to be returned.
     * @param        $data    Optional data do be returned.
     */
    public static function error( string $message, $data = null, $status_code = null )
    {
        $instance = new JsonResponse(false, $message, $data);
        $instance->http_status = $status_code ?? 422;

        return $instance->errorResponse();
    }

    // GETTERS AND SETTERS
    /**
     * Gets the value of success property.
     * @return bool
     */
    public function getSuccess()
    {
        return $this->success;
    }
    
    /**
     * Sets the value of success property
     * @param bool $value
     * @return $this
     */
    public function setSuccess( $value )
    {
        $this->success = $value;
        
        return $this;
    }
    
    /**
     * Gets the value of message property.
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Sets the value of message property.
     * @param string $value
     * @return $this
     */
    public function setMessage( $value )
    {
        $this->message = $value;
        
        return $this;
    }
    
    /**
     * Gets the value of data property.
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Sets the value of data property.
     * @param \stdClass $value
     * @return $this
     */
    public function setData( $value )
    {
        $this->data = $value;
        
        return $this;
    }
    
    public function addData( $property, $value )
    {
        if( is_null($this->data) )
            $this->data = (object) [];
        
        $this->data->{$property} = $value;
    }
    
    /**
     * Indicates if this is a error message.
     * @return bool
     */
    public function isError()
    {
        return ! $this->success;
    }
    
    /**
     * Indicates if this is a success message
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }
    
    /**
     * Returns the AppMessage as Array.
     * @return array
     */
    public function getArray()
    {
        return [ 'success' => $this->success, 'message' => $this->message, 'data' => $this->data ];
    }
}