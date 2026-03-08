<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementPostView implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $postId)
    {
        //
    }

    public function handle(): void
    {
        \App\Models\Post::query()->where('id', $this->postId)->increment('views_count');
    }
}
