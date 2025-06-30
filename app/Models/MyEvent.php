<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Guava\Calendar\Concerns\HasEvents; 
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;

class MyEvent extends Model implements Eventable
{
    use HasFactory; // Model ini MENGGUNAKAN TRAIT Eventable

    protected $table = 'tugas';

    protected $guarded = [];

    protected $casts = [
        'tenggat_waktu' => 'datetime', // <-- Gunakan 'tenggat_waktu' dari tabel 'tugas'
    ];

    public function toCalendarEvent(): CalendarEvent|array
    {
        return CalendarEvent::make($this)
            ->title($this->judul) // Mengambil judul dari kolom 'judul' di tabel 'tugas'
            ->start($this->tenggat_waktu) // Mengambil tanggal mulai dari kolom 'tenggat_waktu'
            ->end($this->tenggat_waktu)   // Mengambil tanggal akhir dari kolom 'tenggat_waktu'
            ->allDay()
            ->backgroundColor(match ($this->status) {
                'terlambat' => 'red',     // Warna merah untuk 'terlambat'
                'segera'    => 'orange',  // Warna oranye untuk 'segera'
                'proses'    => 'blue',    // Warna biru untuk 'proses'
                'selesai'   => 'green',   // Warna hijau untuk 'selesai'
                default     => 'gray',    // Warna abu-abu untuk status lainnya
            });                   // Event ini akan ditampilkan sebagai event sepanjang hari
    }
}
