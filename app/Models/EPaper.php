<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EPaper extends Model
{
    protected $table = 'e_papers';
    protected $fillable = ['edition_date', 'pdf_path', 'is_active'];
    protected $with = ['translations'];

    protected function casts(): array
    {
        return [
            'edition_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(EPaperTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(EPaperTranslation::class)->where('locale', app()->getLocale());
    }

    /**
     * Virtual attributes for easy translation handling.
     */
    public array $translationData = [];

    protected static function booted(): void
    {
        static::saved(function (EPaper $epaper) {
            foreach (['bn', 'en'] as $locale) {
                if (isset($epaper->translationData[$locale])) {
                    $epaper->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $epaper->translationData[$locale]
                    );
                }
            }
        });

        static::deleted(function (EPaper $epaper) {
            $epaper->translations()->delete();
        });
    }

    public function setTitleBnAttribute($value) { $this->translationData['bn']['title'] = $value; }
    public function setTitleEnAttribute($value) { $this->translationData['en']['title'] = $value; }

    public function getTitleBnAttribute() { return $this->translations->where('locale', 'bn')->first()?->title; }
    public function getTitleEnAttribute() { return $this->translations->where('locale', 'en')->first()?->title; }
}
