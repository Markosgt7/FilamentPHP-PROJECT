<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use App\Filament\Personal\Resources\TimesheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Ramsey\Uuid\Type\Time;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();
        if ($lastTimesheet == null) {
            return [
                Action::make('InWork')
                    ->label('Entrar a trabajar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () {
                        $user = Auth::user();
                        $timesheet = new Timesheet();
                        $timesheet->calendar_id = 1;
                        $timesheet->user_id = $user->id;
                        $timesheet->day_in = Carbon::now();
                        $timesheet->type = 'work';
                        $timesheet->save();
                    }),
                Actions\CreateAction::make()
            ];
        }
        return [
            Action::make('InWork')
                ->label('Entrar a trabajar')
                ->color('success')
                ->visible(!$lastTimesheet->day_out == null)
                ->disabled($lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () {
                    $user = Auth::user();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                    Notification::make()
                        ->title('Ha marcado el inicio de trabajo')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->success()
                        ->send();
                }),
            Action::make('OutWork')
                ->label('Salir de trabajar')
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    Notification::make()
                        ->title('Ha marcado el fin de trabajo')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->success()
                        ->send();
                }),
            Action::make('InPause')
                ->label('Iniciar pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';
                    $timesheet->save();
                    Notification::make()
                        ->title('Ha iniciado una Pausa')
                        ->icon('heroicon-o-check')
                        ->color('info')
                        ->info()
                        ->send();
                }),
            Action::make('StopPause')
                ->label('Parar Pausa')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use ($lastTimesheet) {
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();
                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';
                    $timesheet->save();
                    Notification::make()
                        ->title('Pausa finalizada inicia a trabajar')
                        ->icon('heroicon-o-check')
                        ->color('info')
                        ->info()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
