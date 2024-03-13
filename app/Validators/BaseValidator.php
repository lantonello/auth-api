<?php

namespace App\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Lang;
use App\Exceptions\ApiException;

abstract class BaseValidator
{
    /** @var Illuminate\Validation\Validator */
    protected $validator;

    /**
     * Validates the input array against the rules
     * @param array $input
     */
    public function verify(array $input, array $rules)
    {
        // Creates validator instance
        $this->validator = Validator::make($input, $rules);

        if( $this->validator->fails() )
        {
            $errors = json_decode($this->validator->errors());
            $message = Lang::get('general.api_error');

            throw new ApiException( $message, $errors );
        }
    }

    /**
     * Returns data after validation
     * @return array
     */
    public function getData(): array
    {
        if( is_null($this->validator) )
        {
            return null;
        }

        return $this->validator->validated();
    }
}