<?php

namespace App\Filament\Resources\TugasResource\Widgets;

use App\Models\Tugas;
use Carbon\Carbon;
use App\Filament\Resources\TugasResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;


namespace App\Filament\Resources\TugasResource\Widgets;

use App\Models\Tugas;
use Carbon\Carbon;
use App\Filament\Resources\TugasResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TugasStats extends BaseWidget
{
    protected function getCards(): array
    {
        // Data untuk Card "Pekerjaan Hari Ini" (Tugas aktif dengan tenggat waktu 7 hari ke depan)
        $pekerjaanAktifQuery = Tugas::whereNotIn('status', ['selesai', 'dihentikan']);
        $dataPekerjaanAktif = $this->getDailyChartData($pekerjaanAktifQuery, 'tenggat_waktu');

        // Data untuk Card "Pekerjaan Terlambat"
        $pekerjaanTerlambatQuery = Tugas::where('status', 'terlambat');
        $dataPekerjaanTerlambat = $this->getDailyChartData($pekerjaanTerlambatQuery, 'tenggat_waktu');

        // Data untuk Card "Pekerjaan Selesai" (dihitung berdasarkan kapan diselesaikan)
        $pekerjaanSelesaiQuery = Tugas::where('status', 'selesai');
        $dataPekerjaanSelesai = $this->getDailyChartData($pekerjaanSelesaiQuery, 'updated_at');

        // Data untuk Card "Total Pekerjaan" (dihitung berdasarkan kapan dibuat)
        $totalPekerjaanQuery = Tugas::query();
        $dataTotalPekerjaan = $this->getDailyChartData($totalPekerjaanQuery, 'created_at');


        return [
            Card::make('Pekerjaan Hari Ini', $pekerjaanAktifQuery->clone()->whereDate('tenggat_waktu', today())->count())
                ->icon('heroicon-o-calendar-days')
                ->chart($dataPekerjaanAktif['data'])
                ->url(TugasResource::getUrl('index', [
                    'tablesFilters' => ['tugasHariIni' => true],
                ]))
                ->color('primary'),

            Card::make('Pekerjaan Terlambat', $pekerjaanTerlambatQuery->clone()->count())
                ->icon('heroicon-o-exclamation-circle')
                ->chart($dataPekerjaanTerlambat['data'])
                ->url(TugasResource::getUrl('index', [
                    'tableFilters' => ['status' => ['value' => 'terlambat']],
                ]))
                ->color('danger'),

            Card::make('Pekerjaan Selesai', $pekerjaanSelesaiQuery->clone()->count())
                ->icon('heroicon-o-check-circle')
                ->chart($dataPekerjaanSelesai['data'])
                ->url(TugasResource::getUrl('index', [
                    'tableFilters' => ['status' => ['value' => 'selesai']],
                ]))
                ->color('success'),

            Card::make('Total Pekerjaan', $totalPekerjaanQuery->clone()->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->chart($dataTotalPekerjaan['data'])
                ->url(TugasResource::getUrl('index', [
                    'tableFilters' => [
                        'tugasHariIni' => [
                            'enabled' => false, // <-- Secara eksplisit matikan filter ini
                        ],
                    ],
                ]))

                ->color('gray'),
        ];
    }

    /**
     * Helper function untuk mengambil data chart harian secara efisien.
     *
     * @param Builder $query
     * @param string $dateColumn
     * @return array
     */
    private function getDailyChartData(Builder $query, string $dateColumn): array
    {
        $labels = [];
        $dailyData = [];
        $period = now()->subDays(6)->startOfDay()->toPeriod(now()->endOfDay());

        foreach ($period as $date) {
            $labels[] = $date->format('d M');
            $dailyData[$date->format('Y-m-d')] = 0;
        }

        $dbData = $query->clone()
            ->where($dateColumn, '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw("DATE($dateColumn) as date"),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        foreach ($dbData as $date => $count) {
            if (isset($dailyData[$date])) {
                $dailyData[$date] = $count;
            }
        }

        return [
            'labels' => $labels,
            'data' => array_values($dailyData),
        ];
    }
}
