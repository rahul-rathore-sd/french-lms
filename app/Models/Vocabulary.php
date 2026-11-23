<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `vocabulary`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Vocabulary extends Model
{
    protected $table = 'vocabulary';

    public $timestamps = false;

    protected $guarded = [];
}
