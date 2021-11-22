<?php

namespace App\Http\Requests;

use App\Rules\Area;
use App\Rules\ImageURL;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
            if(!empty($this->input('tags.*.area.*'))) {
                
                $tag_area = array_chunk($this->input('tags.*.area.*'),4);
                //print_r($tag_area);
                //sort the array 

                foreach($tag_area as $index => $value) {
                    sort($value);
                    $tag_area[$index] = $value; 
                }

                if(count($tag_area) !== count(array_filter(array_unique($tag_area,SORT_REGULAR)))){
                    $validator->errors()->add('tags.area', __('posts.duplicate_tag'));
                }                
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [ 

            'title'                    => ['nullable'   ,'string'   ,'max:255'],
            'catagory'                 => ['nullable'   ,'string'   ,'max:255'],
            'public'                   => ['nullable'   ,'boolean'],
            'filename'                 => ['required'   ,new ImageURL(config('path.posts.posts_tmp_url'))],
            'tags'                     => ['nullable'   ,'array'   ,'max:100'],//max number of tags that can be assigned to an image
            'tags.*.area'              => ['required_with:tags','array','size:4'],
            'tags.*.area.*'            => ['required_with:tags','size:2',new Area(config('path.posts.posts_tmp_url'),'filename')],
            'tags.*.labels'            => ['array'              ,'max:100'],
            'tags.*.labels.*'          => ['sometimes'          ,'required','string','max:255'], 
            'tags.*.labels.*.*'        => ['nullable'           ,'max:255'], 

        ];
    }


    protected function failedValidation(Validator $validator) { 
        throw new HttpResponseException(response()->json(['error' => true, 'message' => $validator->errors()], 400)); 
    }
}
