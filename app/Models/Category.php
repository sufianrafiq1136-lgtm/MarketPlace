<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * A category can have many ads.
     */
    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class);
    }
}