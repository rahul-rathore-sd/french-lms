<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `activity`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Activity extends Model
{
    protected $table = 'activity';

    public $timestamps = false;

    protected $guarded = [];
}
