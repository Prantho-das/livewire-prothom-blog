<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count())
                ->description('Total posts across all categories')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Total Categories', Category::count())
                ->description('Organized topics and sections')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('success')
                ->chart([1, 3, 2, 5, 4, 7, 6]),
                
            Stat::make('Registered Users', User::count())
                ->description('Total users in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->chart([4, 6, 8, 12, 10, 15, 20]),
        ];
    }
}
