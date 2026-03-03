<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Post extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'slug', 'author_id', 'featured_image', 
        'is_featured', 'is_breaking', 'status', 'published_at', 'views_count'
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(PostTranslation::class)->where('locale', app()->getLocale());
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Scope for published posts.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Virtual attributes for easy translation handling.
     */
    public array $translationData = [];

    protected static function booted(): void
    {
        static::saved(function (Post $post) {
            foreach (['bn', 'en'] as $locale) {
                if (isset($post->translationData[$locale])) {
                    $post->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $post->translationData[$locale]
                    );
                }
            }
        });
    }

    public function setTitleBnAttribute($value) { $this->translationData['bn']['title'] = $value; }
    public function setExcerptBnAttribute($value) { $this->translationData['bn']['excerpt'] = $value; }
    public function setContentBnAttribute($value) { $this->translationData['bn']['content'] = $value; }
    public function setMetaTitleBnAttribute($value) { $this->translationData['bn']['meta_title'] = $value; }
    public function setMetaDescriptionBnAttribute($value) { $this->translationData['bn']['meta_description'] = $value; }

    public function setTitleEnAttribute($value) { $this->translationData['en']['title'] = $value; }
    public function setExcerptEnAttribute($value) { $this->translationData['en']['excerpt'] = $value; }
    public function setContentEnAttribute($value) { $this->translationData['en']['content'] = $value; }
    public function setMetaTitleEnAttribute($value) { $this->translationData['en']['meta_title'] = $value; }
    public function setMetaDescriptionEnAttribute($value) { $this->translationData['en']['meta_description'] = $value; }

    public function getTitleBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->title; }
    public function getExcerptBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->excerpt; }
    public function getContentBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->content; }

    public function getTitleEnAttribute() { return $this->translations->where('locale', 'en')->first()?->title; }
    public function getExcerptEnAttribute() { return $this->translations->where('locale', 'en')->first()?->excerpt; }
    public function getContentEnAttribute() { return $this->translations->where('locale', 'en')->first()?->content; }

    /**
     * Ultra-fast search scope.
     */
    public function scopeSearch(Builder $query, string $term): void
    {
        $query->whereHas('translations', function ($q) use ($term) {
            $q->where('title', 'LIKE', "%{$term}%")
              ->orWhere('content', 'LIKE', "%{$term}%");
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10)
            ->format('webp');

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(630)
            ->format('webp');
    }
}
