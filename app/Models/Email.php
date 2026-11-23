<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `emails`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Email extends Model
{
    protected $table = 'emails';

    public $timestamps = false;

    protected $guarded = [];
}
