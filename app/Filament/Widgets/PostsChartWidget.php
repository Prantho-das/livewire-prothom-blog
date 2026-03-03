<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\ChartWidget;

class PostsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Posts Published (Last 7 Days)';
    protected static ?int $sort = 2;
    // Making it half width so another chart can sit next to it if needed
    // or full if it's the only one
    // protected int | string | array $columnSpan = 'half';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            // Querying count of posts
            $data[] = Post::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Posts Created',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
