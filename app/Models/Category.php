<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    // These are the fields Laravel is allowed to fill when Category::create() is used.
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
        // One category can be assigned to many marketplace ads.
        return $this->hasMany(Ad::class);
    }
}
