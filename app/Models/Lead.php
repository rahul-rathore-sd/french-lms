<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `leads`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Lead extends Model
{
    protected $table = 'leads';

    public $timestamps = false;

    protected $guarded = [];
}
