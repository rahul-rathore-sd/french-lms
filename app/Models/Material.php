<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A study item (grammar lesson, reading/listening exercise, writing or speaking task). */
class Material extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['body' => 'array', 'publish_on' => 'date'];
    }

    /** Items that are available today (seed content, or daily items whose date has come). */
    public function scopeAvailable($q)
    {
        return $q->where(fn ($w) => $w->whereNull('publish_on')->orWhereDate('publish_on', '<=', now()->toDateString()));
    }
}
