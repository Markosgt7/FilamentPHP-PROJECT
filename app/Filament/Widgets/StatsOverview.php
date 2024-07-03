<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\{User, Timesheet, Holiday};

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEmployees = User::all()->count();
        $totalHolidays = Holiday::where('type', 'pending')->count();
        $totalTimesheets = Timesheet::all()->count();
        return [
            Stat::make('Employees', $totalEmployees)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Pending Holidays', $totalHolidays),
            Stat::make('Timesheets', $totalTimesheets),
        ];
    }
}
