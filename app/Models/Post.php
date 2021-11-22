<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

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
        'user_id',
        'public',
        'title',
        'image_path',
        'catagory',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * Scope a query to only include public posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopePublic($query)
    {
        $query->where('public', 1);
    }

    /**
     * Scope a query to only include public posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeLatestfirst($query)
    {
        $query->orderBy('id', 'desc');
    }
    
    /**
     * Get the user that owns this post.
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    /**
     * Get the user that owns this post.
     *
     * @return void
     */
    public function tag(){
        return $this->hasMany(Tag::class,'posts_id','id');
    }

}
