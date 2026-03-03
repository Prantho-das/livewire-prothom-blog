<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['slug', 'parent_id', 'is_active'];

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(CategoryTranslation::class)->where('locale', app()->getLocale());
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Virtual attributes for easy translation handling.
     */
    public array $translationData = [];

    protected static function booted(): void
    {
        static::saved(function (Category $category) {
            foreach (['bn', 'en'] as $locale) {
                if (isset($category->translationData[$locale])) {
                    $category->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $category->translationData[$locale]
                    );
                }
            }
        });
    }

    public function setNameBnAttribute($value) { $this->translationData['bn']['name'] = $value; }
    public function setDescriptionBnAttribute($value) { $this->translationData['bn']['description'] = $value; }

    public function setNameEnAttribute($value) { $this->translationData['en']['name'] = $value; }
    public function setDescriptionEnAttribute($value) { $this->translationData['en']['description'] = $value; }

    public function getNameBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->name; }
    public function getDescriptionBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->description; }

    public function getNameEnAttribute() { return $this->translations->where('locale', 'en')->first()?->name; }
    public function getDescriptionEnAttribute() { return $this->translations->where('locale', 'en')->first()?->description; }
}
