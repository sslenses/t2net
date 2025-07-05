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
            Card::make('Pekerjaan Hari Ini', Tugas::query() // Mulai dengan query()
                ->whereDate('tenggat_waktu', Carbon::today())
                // --- Tambahkan kondisi ini ---
                ->whereNotIn('status', ['selesai', 'dihentikan'])
                // --- Akhir kondisi tambahan ---
                ->count()) // Kemudian panggil count()
                ->icon('heroicon-o-calendar-days')
                ->url(TugasResource::getUrl('index') . '?tableFilters[tugasHariIni][enabled]=true&tableFilters[tugasHariIni][isActive]=true')
                ->color('warning'),

            Card::make('Pekerjaan Terlambat', Tugas::where('status', 'terlambat')->count())
                // ->description('Melewati tenggat waktu')
                ->icon('heroicon-o-exclamation-circle')
                ->url(TugasResource::getUrl('index', [
                    'tableSearch' => 'terlambat',
                    ]))
                ->color('danger'),

            Card::make('Pekerjaan Selesai', Tugas::where('status', 'selesai')->count())
                ->icon('heroicon-o-exclamation-triangle')
                // ->description('Tugas yang harus segera ditangani')
                ->url(TugasResource::getUrl('index', [
                    'tableSearch' => 'selesai',
                    ]))
                ->color('warning'),

            Card::make('Total Pekerjaan', Tugas::count())
                // ->description('Semua tugas tercatat')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
                    ];
    }
}
