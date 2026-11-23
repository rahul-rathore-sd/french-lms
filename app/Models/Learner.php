<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Learner extends Model
{
    protected $guarded = [];

    protected $hidden = ['pin_hash'];

    /** Hours already mastered when starting at each level (matches config/tcf.php stages). */
    public const CREDIT = ['A1' => 0, 'A2' => 70, 'B1' => 160, 'B2' => 295];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'last_seen_at' => 'datetime'];
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(LearnerToken::class);
    }

    public function states(): HasMany
    {
        return $this->hasMany(LearnerState::class);
    }
}
