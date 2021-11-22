<?php

namespace App\Rules;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Area implements Rule
{
    private $path = '';
    private $input_name = '';
    private $message = '';
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($path='',$input_name='')
    {
        $this->path = $path;
        $this->input_name = $input_name;
    }

    function pnpoly($nvert, $vertx, $verty, $testx, $testy)
    {
      $i = 0; $j = 0; $c = 0;
      for ($i = 0, $j = $nvert-1; $i < $nvert; $j = $i++) {
        if ( (($verty[$i]>$testy) != ($verty[$j]>$testy)) &&
         ($testx < ($vertx[$j]-$vertx[$i]) * ($testy-$verty[$i]) / ($verty[$j]-$verty[$i]) + $vertx[$i]) )
           $c = !$c;
      }
      return $c;
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

        if(!is_numeric($value[0]) || !is_numeric($value[1]))  {
            $this->message = __('posts.invalid_input');
            return false;
        }

        if(!request()->has($this->input_name))  {
            $this->message = __('posts.post_file_not_found');
            return false;
        }

        $filename =  request()->get('filename');       


        $url = '';
        if($this->path !== '')
            $url .= $this->path.'/';
        
        $url .= $filename;
        
        if(Storage::missing($url)){
            $this->message = __('posts.post_file_not_found');
            return false;
        }

        $contents = Storage::get($url);
    
        $height = Image::make($contents)->height();
        
        $width = Image::make($contents)->width();


        if( $width == 0 || $height == 0 ){
           $this->message = __('posts.tag_not_found'); 
           return false;
        }
        $x = $value[0];
        $y = $value[1];

        $vertx = [0,$width,$width,0,0];
        $verty = [0,0,$height,$height,0];


        if(!$this->pnpoly(4,$vertx,$verty,$x,$y)){
           $this->message = __('posts.tag_out_of_bounds',['attribute'=>':attribute']); 
           return false;
        }

        return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
