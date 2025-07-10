<?php

namespace App\Filament\Widgets;

use App\Models\MyEvent; // Menggunakan model MyEvent
use App\Models\Tugas;   // Digunakan untuk CreateAction
use Filament\Forms;
use Filament\Support\RawJs;
use Filament\Actions\CreateAction;
use Carbon\Carbon; // Untuk memformat tanggal
use App\Filament\Resources\TugasResource; // Import TugasResource untuk URL event
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Closure;


class MyCalendarWidget extends CalendarWidget
{
    // protected string|Closure|HtmlString|null $heading = 'Kalender Tugas';

    // Ini adalah metode untuk mengambil event dari database
    public function getEvents(array $fetchInfo = []): Collection | array
    {
        $events = MyEvent::query()
            // --- PERBAIKAN DI SINI: Gunakan 'tenggat_waktu' ---
            ->whereNotNull('tenggat_waktu')
            ->get();

        return $events;
    }


    protected bool $dateClickEnabled = true;

    public function onDateClick(array $info = []): void
    {
        $date = Carbon::parse($info['dateStr'])->format('Y-m-d');

        // --- INI PERUBAHAN PENTINGNYA ---
        // Dapatkan URL dasar resource tanpa parameter apapun
        $this->redirect(TugasResource::getUrl('index', [
            'tableSearch' => $date, // Mengaktifkan pencarian tanggal
            'tableFilters' => [
                'tugasHariIni' => false, // <--- SECARA EKSPILSIT NONAKTIFKAN FILTER INI
            ],
        ]));
        // --- AKHIR PERUBAHAN PENTING ---
    }
}
