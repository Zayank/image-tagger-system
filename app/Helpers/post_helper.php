<?php
namespace App\Helpers;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;

class post_helper
{
    public static function getpolygon($area)
    {
        
        $points = [];
        foreach($area AS $area_row){
                
            $points[] = new Point($area_row[1],$area_row[0]);     
                
        }

        //The stating and ending points of the polygon has to be the same,otherwise not saving into DB.
        $points[] = $points[0];

        return new Polygon([new LineString($points)], 0);
                
    }
}

