<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class Tag extends Model
{
    use HasFactory,SpatialTrait;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';    

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'posts_id',
        'area',
        'labels',
    ];

    protected $spatialFields = [
        'area'
    ];

    /**
     * Get the post of the tag.
     *
     * @return void
     */
    public function post(){
        return $this->belongsTo(Post::class,'posts_id','id');
    }
}
