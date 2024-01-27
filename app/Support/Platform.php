<?php

namespace App\Support;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Platform: int implements HasColor, HasIcon, HasLabel
{
    case Mobile = 1;
    case Web = 2;
    case Desktop = 3;

    public function getColor(): string | array | null
    {
        return 'primary';
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Mobile => 'heroicon-o-device-phone-mobile',
            self::Web => 'heroicon-o-globe-alt',
            self::Desktop => 'heroicon-o-computer-desktop',
        };
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Mobile => 'Mobile App',
            self::Web => 'Web Client',
            self::Desktop => 'Desktop Client',
        };
    }
}
