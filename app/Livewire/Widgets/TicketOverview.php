<?php

namespace App\Livewire\Widgets;

use App\Models\Ticket;
use App\Support\Status;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Collection;

class TicketOverview extends BaseWidget
{
    public Collection $tickets;

    protected function getStats(): array
    {
        [$open, $others] = $this->tickets->partition(fn (Ticket $ticket) => $ticket->status === Status::Open);
        [$ongoing, $others] = $others->partition(fn (Ticket $ticket) => $ticket->status === Status::Ongoing);
        [$resolved, $unresolved] = $others->partition(fn (Ticket $ticket) => $ticket->status === Status::Resolved);

        return [
            Stat::make('Open', $open->count())
                ->icon('heroicon-o-ticket'),
            Stat::make('Ongoing', $ongoing->count())
                ->icon('heroicon-o-clock'),
            Stat::make('Unresolved', $unresolved->count())
                ->icon('heroicon-m-x-mark'),
            Stat::make('Resolved', $resolved->count())
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
