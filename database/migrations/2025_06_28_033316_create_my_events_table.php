<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('my_events', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // Judul event, akan mengambil dari tabel tugas.judul atau tugas.tugas_id
            $table->dateTime('start'); // Waktu mulai event, akan mengambil dari tugas.tenggat_waktu
            // $table->dateTime('end'); // Kolom 'end' dihilangkan sesuai permintaan
            $table->timestamps(); // Timestamps, sama dengan tabel tugas
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_events');
    }
};