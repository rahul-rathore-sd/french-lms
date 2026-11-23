<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `social_posts`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class SocialPost extends Model
{
    protected $table = 'social_posts';

    public $timestamps = false;

    protected $guarded = [];
}
