<?php
namespace App\Helpers;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;

class storage_helper
{

    public static function get_publishable_post_storage_path($path)
    {
        
        return asset(config('path.storage') . '/' . $path);
                
    }

    public static function get_tmp_post_link($path)
    {
        
        return config('path.posts.posts_tmp_url') . '/' . $path;
                
    }

    public static function get_tmp_publishable_post_link($path)
    {
        
        return asset(config('path.posts.posts_tmp_url') . '/' . $path);
                
    }

    public static function get_post_link($path)
    {
        
        return config('path.posts.posts_url') . '/' . $path;
                
    }

    public static function get_publishable_post_link($path)
    {
        
        return asset(config('path.posts.posts_url') . '/' . $path);
                
    }

}

