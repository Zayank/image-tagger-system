<?php

namespace App\Rules;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Validation\Rule;

class ImageURL implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $path = '';
    public function __construct($path = '')
    {
        $this->path = $path;
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
        $url = '';
        if($this->path !== '')
            $url .= $this->path.'/';
        $url .= $value;
        if(Storage::exists($url)){
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('posts.post_file_not_found');
    }
}
