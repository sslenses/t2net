<?php

namespace App\Filament\Widgets;

use App\Models\MyEvent; // Menggunakan model MyEvent
use App\Models\Tugas;   // Digunakan untuk CreateAction
use Filament\Forms;
use Filament\Actions\CreateAction;
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
}