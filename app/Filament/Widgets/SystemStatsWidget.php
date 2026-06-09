<?php

namespace App\Filament\Widgets;

use App\Models\Bank;
use App\Models\Contact;
use App\Models\Guider;
use App\Models\Hotel;
use App\Models\Place;
use App\Models\Quest;
use App\Models\Restaurant;
use App\Models\RewardLog;
use App\Models\Review;
use App\Models\State;
use App\Models\Trip;
use App\Models\User;
use App\Models\Visit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SystemStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])
                ->url(route('filament.admin.resources.users.index')),

            Stat::make('Total Places', Place::count())
                ->description('Across ' . State::count() . ' states')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('primary')
                ->url(route('filament.admin.resources.places.index')),

            Stat::make('Total Hotels', Hotel::count())
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info')
                ->url(route('filament.admin.resources.hotels.index')),

            Stat::make('Total Restaurants', Restaurant::count())
                ->descriptionIcon('heroicon-m-cake')
                ->color('warning')
                ->url(route('filament.admin.resources.restaurants.index')),

            Stat::make('Total Banks', Bank::count())
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray')
                ->url(route('filament.admin.resources.banks.index')),

            Stat::make('Total Visits', Visit::count())
                ->description('Check-ins recorded')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success')
                ->url(route('filament.admin.resources.visits.index')),

            Stat::make('Total Trips', Trip::count())
                ->descriptionIcon('heroicon-m-map')
                ->color('primary')
                ->url(route('filament.admin.resources.trips.index')),

            Stat::make('Total Reviews', Review::count())
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->url(route('filament.admin.resources.reviews.index')),

            Stat::make('Active Quests', Quest::count())
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->url(route('filament.admin.resources.quests.index')),

            Stat::make('XP Awarded', number_format(RewardLog::sum('points')))
                ->description('All-time experience points')
                ->descriptionIcon('heroicon-m-gift')
                ->color('success')
                ->url(route('filament.admin.resources.reward-logs.index')),

            Stat::make('Pending Contacts', Contact::where('is_approved', false)->count())
                ->description('Awaiting approval')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger')
                ->url(route('filament.admin.resources.contacts.index')),

            Stat::make('Verified Guiders', Guider::where('is_verified', true)->count())
                ->description('of ' . Guider::count() . ' total')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('info')
                ->url(route('filament.admin.resources.guiders.index')),
        ];
    }
}
