<?php

namespace App\Livewire\Pages;

use App\Models\Ticket;
use App\Support\Platform;
use App\Support\Status;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Tickets extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public static function eloquentQuery(): Builder
    {
        return Ticket::query()
            ->whereBelongsTo(auth()->user())
            ->with(['updates' => function ($query) {
                $query->orderBy('status', 'ASC');
            }])
            ->latest();
    }

    public static function formComponents(): array
    {
        return [
            Forms\Components\ToggleButtons::make('platform')
                ->required()
                ->inline()
                ->options(Platform::class),
            Forms\Components\TextInput::make('title')
                ->required()
                ->string()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->required()
                ->string(),
            Forms\Components\SpatieMediaLibraryFileUpload::make('attachments')
                ->collection('attachments')
                ->required()
                ->image()
                ->multiple(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Ticket submission')
            ->description('Place where you can monitor your ticket')
            ->query($this->eloquentQuery())
            ->recordUrl(fn (Ticket $record) => route('tickets.show', [$record]))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('platform'),
                Tables\Columns\TextColumn::make('created_at')
                    ->date(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date(),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make('New ticket')
                    ->modalHeading('New ticket')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->using(function (array $data, Tables\Actions\CreateAction $action): Ticket {
                        try {
                            DB::beginTransaction();

                            $ticket = Ticket::query()->create($data);

                            $ticket->updates()->create([
                                'user_id' => auth()->id(),
                                'message' => Status::Open->message(auth()->user()->name),
                            ]);
                        } catch (\Throwable $th) {
                            DB::rollBack();

                            Notification::make()
                                ->title('Ticket creation failed, try again.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        DB::commit();
                        return $ticket;
                    })
                    ->successNotificationTitle('New ticket created')
                    ->form($this->formComponents())
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn (Ticket $record) => $record->status !== Status::Open)
                    ->form($this->formComponents()),
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->icon('heroicon-o-chevron-right')
                    ->url(
                        fn (Ticket $record) => route('tickets.show', [$record])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make('delete'),
            ]);
    }

    public function render()
    {
        return view('livewire.pages.tickets');
    }
}
