<?php

namespace App\Support;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Status: int implements HasColor, HasLabel
{
    case Open = 1;
    case Ongoing = 2;
    case Unresolved = 3;
    case Resolved = 4;

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Open => 'warning',
            self::Ongoing => 'info',
            self::Unresolved => 'danger',
            self::Resolved => 'success',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Ongoing => 'Ongoing',
            self::Unresolved => 'Unresolved',
            self::Resolved => 'Resolved',
        };
    }

    public function message(string $subject): string
    {
        return match ($this) {
            self::Open => "{$subject} open new ticket",
            self::Ongoing => "{$subject} process the ticket",
            self::Unresolved => "{$subject} mark the ticket as unresolved",
            self::Resolved => "{$subject} mark the ticket as resolved",
        };
    }
}
