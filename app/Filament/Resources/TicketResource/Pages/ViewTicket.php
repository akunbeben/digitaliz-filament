<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use App\Models\Ticket;
use App\Support\Status;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('Process')
                ->hidden($this->getRecord()->latestUpdate?->status !== Status::Open)
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->modalHeading('Process this ticket')
                ->requiresConfirmation()
                ->action(function (Ticket $record, Actions\Action $action) {
                    try {
                        DB::beginTransaction();
                        $record->updates()->create([
                            'user_id' => auth()->id(),
                            'message' => Status::Ongoing->message(auth()->user()->name),
                            'status' => Status::Ongoing,
                        ]);
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        Notification::make()
                            ->title('Ticket update failed, try again.')
                            ->danger()
                            ->send();

                        $action->halt();
                    }

                    DB::commit();
                    return $record;
                }),
            Actions\ActionGroup::make([
                Actions\Action::make('Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('notes'),
                    ])
                    ->modalHeading('Mark ticket as Resolved')
                    ->requiresConfirmation()
                    ->action(function (Ticket $record, Actions\Action $action) {
                        try {
                            DB::beginTransaction();
                            $record->update(['status' => Status::Resolved]);

                            $record->updates()->create([
                                'user_id' => auth()->id(),
                                'message' => Status::Resolved->message(auth()->user()->name),
                                'status' => Status::Resolved,
                            ]);
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Ticket update failed, try again.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        DB::commit();
                        return $record;
                    }),
                Actions\Action::make('Unresolved')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('notes'),
                    ])
                    ->modalHeading('Mark ticket as Unresolved')
                    ->requiresConfirmation()
                    ->action(function (Ticket $record, Actions\Action $action) {
                        try {
                            DB::beginTransaction();
                            $record->update(['status' => Status::Unresolved]);

                            $record->updates()->create([
                                'user_id' => auth()->id(),
                                'message' => Status::Unresolved->message(auth()->user()->name),
                                'status' => Status::Unresolved,
                            ]);
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Ticket update failed, try again.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        DB::commit();
                        return $record;
                    }),
            ])
                ->hidden($this->getRecord()->latestUpdate?->status !== Status::Ongoing)
                ->color('gray')
                ->button()
                ->label('Mark as')
                ->icon('heroicon-o-paper-airplane'),
        ];
    }
}
