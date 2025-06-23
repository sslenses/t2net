<?php

namespace App\Filament\Resources\TugasResource\Widgets;

use App\Models\Tugas;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TugasStats extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Segera Ditangani', Tugas::where('status', 'segera')->count())
                ->icon('heroicon-o-exclamation-triangle')
                // ->description('Tugas yang harus segera ditangani')
                ->color('warning'),

            Card::make('Total Tugas', Tugas::count())
                // ->description('Semua tugas tercatat')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Card::make('Tugas Terlambat', Tugas::whereDate('tenggat_waktu', '<', now())->count())
                // ->description('Melewati tenggat waktu')
                ->icon('heroicon-o-exclamation-circle')
                ->color('danger'),

            Card::make('Tugas Hari Ini', Tugas::whereDate('tenggat_waktu', now())->count())
                // ->description('Jatuh tempo hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('warning'),
        ];
    }
}
