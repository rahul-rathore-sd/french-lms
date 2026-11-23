<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnerState extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['updated_at' => 'datetime'];
    }
}
