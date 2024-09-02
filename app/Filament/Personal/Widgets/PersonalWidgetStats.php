<?php

namespace App\Filament\Personal\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Holiday;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pendign Holidays', $this->getPendingHoliday(Auth::user())),
            Stat::make('Aproved Holidays', $this->getApprovedHoliday(Auth::user())),
            Stat::make('Total Works', $this->getTotalWork(Auth::user())),
            Stat::make('Total Pause', $this->getTotalPause(Auth::user())),
        ];
    }

    protected function getPendingHoliday(User $user)
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'pending')->get()->count();
        return $totalPendingHolidays;
    }
    protected function getApprovedHoliday(User $user)
    {
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'approved')->get()->count();
        return $totalApprovedHolidays;
    }
    protected function getTotalWork(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->whereDate('created_at',Carbon::today())->get();

        $sumHours = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumHours = $totalDuration + $sumHours;
        }
        $tiempoFormato = gmdate("H:i:s", $sumHours);
        return $tiempoFormato;
    }
    protected function getTotalPause(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
        ->where('type', 'pause')->whereDate('created_at',Carbon::today())->get();
        $sumHours = 0;
        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);
            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumHours = $totalDuration + $sumHours;
            }
            $tiempoFormato = gmdate("H:i:s", $sumHours);
            return $tiempoFormato;
    }
}
