<?php

namespace App\Livewire\Pages;

use App\Models\Ticket;
use App\Models\Update;
use App\Support\Status;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists;
use JaOcero\ActivityTimeline;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Layout('layouts.app')]
class ViewTicket extends Component implements HasForms, HasInfolists
{
    use InteractsWithInfolists;
    use InteractsWithForms;

    #[Locked]
    public Ticket $ticket;

    public function ticketInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->ticket)
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
                            ->columns(2)
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
                                'updates' => fn ($query) => $query->orderBy('status', 'DESC')->latest()
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

    public function mount(Ticket $ticket): void
    {
        abort_if(!$ticket->user()->is(auth()->user()), 404);

        $this->ticket = $ticket;
    }

    public function render()
    {
        return view('livewire.pages.view-ticket');
    }
}
