<?php

namespace App\Filament\Widgets;

use App\Models\Place;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Bank;
use Filament\Widgets\ChartWidget;

class ContentDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Content distribution by state';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $places = Place::count();
        $hotels = Hotel::count();
        $restaurants = Restaurant::count();
        $banks = Bank::count();

        return [
            'datasets' => [
                [
                    'label' => 'Content',
                    'data' => [$places, $hotels, $restaurants, $banks],
                    'backgroundColor' => ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
                ],
            ],
            'labels' => ['Places', 'Hotels', 'Restaurants', 'Banks'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
