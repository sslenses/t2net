<?php

namespace App\Filament\Resources\TugasResource\Widgets;

use App\Models\Tugas;
use Carbon\Carbon;
use App\Filament\Resources\TugasResource;
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
                 ->url(TugasResource::getUrl('index', [
        'tableSearch' => 'segera',
    ]))
                ->color('warning'),

            Card::make('Total Tugas', Tugas::count())
                // ->description('Semua tugas tercatat')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Card::make('Tugas Terlambat', Tugas::whereDate('tenggat_waktu', '<', now())->count())
                // ->description('Melewati tenggat waktu')
                ->icon('heroicon-o-exclamation-circle')
                ->url(TugasResource::getUrl('index', [
                    'tableSearch' => 'terlambat',
                    ]))
                ->color('danger'),

            Card::make('Tugas Hari Ini', Tugas::whereDate('tenggat_waktu', Carbon::today())->count())
    ->icon('heroicon-o-calendar-days')
    ->color('warning')
    ->url(TugasResource::getUrl('index') . '?tableFilters[tugasHariIni][enabled]=true&tableFilters[tugasHariIni][isActive]=true')


        ];
    }
}
