<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Table `clients`: ISO-8601 string timestamps are set by the controllers, so Eloquent timestamps are off. */
class Client extends Model
{
    protected $table = 'clients';

    public $timestamps = false;

    protected $guarded = [];
}
