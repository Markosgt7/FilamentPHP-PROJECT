<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Filament\Resources\HolidayResource;
use App\Mail\HolidayApproved;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        // send email if approved
        if ($record->type === 'approved') {
            $user = User::find($record->user_id);
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            );
            Mail::to($user)->send(new HolidayApproved($data));
        }
        if ($record->type === 'decline') {
            $user = User::find($record->user_id);
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            );
            Mail::to($user)->send(new HolidayApproved($data));
        }

        return $record;
    }
}
