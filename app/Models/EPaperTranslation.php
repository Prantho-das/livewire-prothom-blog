<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EPaperTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['e_paper_id', 'locale', 'title'];

    public function ePaper(): BelongsTo
    {
        return $this->belongsTo(EPaper::class);
    }
}
