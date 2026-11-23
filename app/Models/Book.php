<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `books`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Book extends Model
{
    protected $table = 'books';

    public $timestamps = false;

    protected $guarded = [];
}
