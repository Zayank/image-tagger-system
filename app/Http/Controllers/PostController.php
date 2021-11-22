<?php

namespace App\Http\Controllers;

/**
 * API for the viewing,managing posts.
 * @package image-tagger
 * @subpackage PostController
 * @author Zayan K
 *
 * @see PostController::index() for viewing all public posts
 * @see PostController::show() for viewing post details of current user
 * @see PostController::users_posts() for viewing all the posts of current user
 * @see PostController::update() for updating title,catagory,public,tag details of a post
 * @see PostController::upload_file() for temporarly storing the image before mapping to a post
 * @see PostController::store() for posting an image
 * 
 */


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Tag;
use App\Models\Post;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialExpression;
use Intervention\Image\Facades\Image;
use App\Helpers\Post_helper;
use App\Helpers\Storage_helper;


class PostController extends Controller
{

    /**
    *
    * @return \Illuminate\Http\Response
    */
   
    public function index() {

        $posts = Post::public()->count();
        
        if( $posts === 0 )  {
            
            return response()->json('', 204);
        
        }

        $posts = Post::public()
                    ->latestfirst()
                    ->simplePaginate();


        foreach($posts AS $index => $post)  {
        
                    if(isset($post->image_path) && !empty($post->image_path))   {
                        
                        $posts[$index]["image_path"] = Storage_helper::get_publishable_post_storage_path($post->image_path);
    
                    }

        }

        return response()->json(['error' => false, 'data' => $posts], 200);
    
    }

    /**
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show($id)   {

        $user_id = auth()->user()->id;
        
        $post =  Post::where('id',$id)
                     ->where('user_id',$user_id)
                     ->first();

        
        if( null === $post )  {

            return response()->json(['error' => true, 'message' => __('posts.invalid_request')], 400);

        }

         $data = array();
         $data  =$post;
         $data["image_path"] = Storage_helper::get_publishable_post_storage_path($post->image_path);

         $tag  = $post->tag();
         
         $tag_tmp = array();
         if(null !== $tag)  {
            
            $tag  = $post->tag()->get();

            foreach($tag AS $tag_row)  {            

            $tmp = array();
            $area = json_decode($tag_row->area->toJson(),true);
            if(array_key_exists('coordinates', $area) && isset($area['coordinates'][0])) {
                $tmp['area']    = $area['coordinates'][0];
            
                array_pop($tmp['area']);

                $tmp['labels']  = json_decode($tag_row->labels,true);

                $tag_tmp[] = $tmp;
            }

         }

         }
         
         $data['tags'] = $tag_tmp;

         return response()->json(['error' => false, 'data' => $data], 200);

    }

    /**
    *
    * @return \Illuminate\Http\Response
    */

    public function users_posts() {

        $user_id = auth()->user()->id;

        $posts = Post::where('user_id',$user_id)->count();
        
        if( $posts === 0 )  {
            
            return response()->json('', 204);
        
        }


        $post = Post::where('user_id',$user_id)
                    ->latestfirst()
                    ->simplePaginate();


        return response()->json(['error' => false, 'data' => $post], 200);
        
    }

    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function update(UpdatePostRequest $request, $id){
        
        $user_id = auth()->user()->id;
        
        $post =  Post::where('id',$id)
                     ->where('user_id',$user_id)
                     ->first();

        // Retrieve the validated input data...
        $validated = $request->validated();

        $fields = $request->safe()->all();

        $post->update($fields);

        //Replacing existing tags for new ones,not required in case Tag::firstOrNew section is used
        $post->tag()->delete();
            

        //if tag fields are provided,will be replaced with new ones 
        if(isset($fields['tags']) && is_array($fields['tags'])) {
                
            foreach($fields['tags'] AS $tag_row){

                $tag = new Tag();

                $tag->posts_id = $post->id;

                $tag->labels = !empty($tag_row['labels']) ? json_encode($tag_row['labels']) : null;

                $tag->area = Post_helper::getpolygon($tag_row['area']);

                $tag->save();
                
                /*
                //code section for updating or adding based on tagged section and posts id
        
                $tag = Tag::firstOrNew(['posts_id' => $post->id, 'area' => $area->toWKT()]);
                $tag->labels = json_encode($tag_row['labels']);
                $tag->area = $area;
                $tag->save();

                */
                
            }
        }
        

        return response()->json(['error' => false, 'message' => __('posts.post_updated')], 200);
    
    }

    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store(StorePostRequest $request) {

        // Retrieve the validated input data...
        $validated = $request->validated();

        $fields = $request->safe()->all();

        //move image from temp folder
        //useful for later removing files which were uploaded but not used 

        Storage::move(Storage_helper::get_tmp_post_link($fields['filename']), Storage_helper::get_post_link($fields['filename']));

        $fields['image_path'] = Storage_helper::get_post_link($fields['filename']);

        $fields['user_id'] = auth()->user()->id;

        $post = Post::create($fields);

        if(isset($fields['tags']) && null !== $fields['tags'])  {

            foreach($fields['tags'] AS $tag_row) {
                
                $tag = new Tag();

                $tag->posts_id = $post->id;

                $tag->labels = !empty($tag_row['labels']) ? json_encode($tag_row['labels']) : null;

                $tag->area = Post_helper::getpolygon($tag_row['area']);
                
                $tag->save();
                
        }

        }

        return response()->json(['error' => false, 'message' => __('posts.post_created'),'data' => [ "id" => $post->id ]], 201);
    
    }

    /**
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function upload_file(Request $request)
    {
         $validator = Validator::make($request->all(), [  
            
            'image'                    => 'required|mimes:png,jpg,jpeg,gif|max:102445'//Accepts image with max size of 45MB

        ]);
        
        // return error if validation fails
        if ($validator->fails()) { 
            
            return response()->json(['error' => true, 'message' => $validator->errors()], 400);         
        
        }

        $fields = $validator->valid();
        
        $image = $request->image;
        
        $fields['user_id'] = auth()->user()->id;
        
        //system assigns unique name for file
        //the image is placed inside tmp file till it is mapped to a post

        $url = Storage::putFile(
                        config('path.posts.posts_tmp_url'), $request->file('image')
                        ); 

        
        if(Storage::exists($url)){

                $height = Image::make($image)->height();
        
                $width = Image::make($image)->width();            

                $uploadedImageResponse = array(
                                                "filename"      =>  basename($url),    
                                                "image_url"     =>  Storage_helper::get_publishable_post_storage_path($url),
                                                "dimension"     =>  array(
                                                                            "width"         => $width,
                                                                            "height"        => $height,
                                                                            "unit"          => "pixels"
                                                                        ),
                                                "mime"          => $image->getClientMimeType(),
                                            );

                return response()->json(['error' => false, 'data' => $uploadedImageResponse], 201);         
        
        } else {

            return response()->json(['error' => true, 'message' => __('posts.file_upload_failed')], 400);
        }
    
    }

}
