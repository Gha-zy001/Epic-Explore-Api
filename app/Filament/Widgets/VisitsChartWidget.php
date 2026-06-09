<?php

namespace App\Filament\Widgets;

use App\Models\RewardLog;
use App\Models\Visit;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VisitsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Visits (last 14 days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Visit::whereDate('created_at', $date)->count();
            $data->push([
                'date' => $date->format('M d'),
                'count' => $count,
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Visits',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
