<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['slug'];

    public function translations(): HasMany
    {
        return $this->hasMany(TagTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(TagTranslation::class)->where('locale', app()->getLocale());
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
