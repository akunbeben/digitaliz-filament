<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use App\Models\Update;
use App\Support\Platform;
use App\Support\Status;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use JaOcero\ActivityTimeline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
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
                        ->image()
                        ->required()
                        ->multiple(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\Section::make()
                        ->columnSpan(2)
                        ->columns(3)
                        ->schema([
                            Infolists\Components\TextEntry::make('title'),
                            Infolists\Components\TextEntry::make('description')
                                ->columnSpanFull(),
                            Infolists\Components\SpatieMediaLibraryImageEntry::make('attachments')
                                ->extraImgAttributes(['class' => 'border rounded-lg'])
                                ->collection('attachments')
                                ->columns(4)
                                ->columnSpanFull(),
                        ]),
                    Infolists\Components\Group::make([
                        Infolists\Components\Section::make()
                            ->columnSpan(1)
                            ->schema([
                                Infolists\Components\TextEntry::make('platform'),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->dateTime(),
                            ]),
                    ]),
                    ActivityTimeline\Components\ActivitySection::make('updates')
                        ->columnSpan(2)
                        ->label('Ticket updates')
                        ->state(
                            fn (Ticket $record) => $record->load([
                                'updates' => fn ($query) => $query->latest()->orderBy('status', 'DESC')
                            ])->updates
                        )
                        ->schema([
                            ActivityTimeline\Components\ActivityTitle::make('message'),
                            ActivityTimeline\Components\ActivityDescription::make('status')
                                ->getStateUsing(fn (Update $record): string | null => $record->status->getLabel()),
                            ActivityTimeline\Components\ActivityDate::make('updated_at'),
                            ActivityTimeline\Components\ActivityIcon::make('status')
                                ->icon(fn (Status $state): string | null => match ($state) {
                                    $state::Open => 'heroicon-m-ticket',
                                    $state::Ongoing => 'heroicon-o-clock',
                                    $state::Unresolved => 'heroicon-o-x-mark',
                                    $state::Resolved => 'heroicon-o-check',
                                    default => null,
                                })
                                ->color(fn (Status $state): string | null => match ($state) {
                                    $state::Open => 'primary',
                                    $state::Ongoing => 'info',
                                    $state::Unresolved => 'danger',
                                    $state::Resolved => 'success',
                                    default => 'zinc',
                                }),
                        ]),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Filters\SelectFilter::make('platform')
                    ->options(Platform::class)
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UpdatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'view' => Pages\ViewTicket::route('/{record}'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
