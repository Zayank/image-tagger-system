<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'We have emailed your password reset link!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",
    'required' => 'The :attribute field is required.',
    'has_lower_char' => 'The :attribute field must be at least one lowercase letter.',
    'has_upper_char' => 'The :attribute field must be at least one uppercase letter.',
    'has_numeral' => 'The :attribute field must have at least one number.',
    'special_character' => 'The :attribute field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>ยง~'),
    'min_length' => 'The :attribute field must be at least 5 characters in length.',
    'max_length' => 'The :attribute field cannot exceed 32 characters in length.'
];
