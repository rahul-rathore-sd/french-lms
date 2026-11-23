<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `blog_posts`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class BlogPost extends Model
{
    protected $table = 'blog_posts';

    public $timestamps = false;

    protected $guarded = [];
}
