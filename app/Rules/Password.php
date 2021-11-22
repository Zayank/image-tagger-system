<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Password implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $error_message = '';


    public function __construct()
    {
        //
    }
    function valid_password($password = '')
    {
        
        $regex_lowercase = '/[a-z]/';
        $regex_uppercase = '/[A-Z]/';
        $regex_number = '/[0-9]/';
        $regex_special = '/[!@#$%^&*()\-_=+{};:,<.>ยง~]/';

        if (empty($password))
        {
            $this->error_message = __('passwords.required',['attribute'=>':attribute']);

            return FALSE;
        }

        if (preg_match_all($regex_lowercase, $password) < 1)
        {
            $this->error_message =  __('passwords.has_lower_char',['attribute'=>':attribute']);

            return FALSE;
        }

        if (preg_match_all($regex_uppercase, $password) < 1)
        {
            $this->error_message =  __('passwords.has_upper_char',['attribute'=>':attribute']);

            return FALSE;
        }

        if (preg_match_all($regex_number, $password) < 1)
        {
            $this->error_message =  __('passwords.has_numeral',['attribute'=>':attribute']);

            return FALSE;
        }

        if (preg_match_all($regex_special, $password) < 1)
        {
            $this->error_message =  __('passwords.special_character',['attribute'=>':attribute']);

            return FALSE;
        }

        if (strlen($password) < 5)
        {
            $this->error_message =  __('passwords.min_length',['attribute'=>':attribute']);

            return FALSE;
        }

        if (strlen($password) > 32)
        {
            $this->error_message =  __('passwords.max_length',['attribute'=>':attribute']);

            return FALSE;
        }

        return TRUE;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->valid_password($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {

        return $this->error_message;
    }
}
