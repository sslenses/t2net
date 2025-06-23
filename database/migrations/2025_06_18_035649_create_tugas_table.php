<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();

            // ID unik khusus tugas
            $table->string('tugas_id')->unique();

            // Judul atau keterangan pelanggan
            $table->string('judul')->nullable();

            // Relasi ke user sebagai pelanggan
            $table->foreignId('pelanggan_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status ENUM dalam huruf kecil
            $table->enum('status', [
                '-', 
                'review', 
                'proses', 
                'segera', 
                'terlambat', 
                'selesai', 
                'dihentikan', 
                'dibatalkan'
            ])->default('-');

            // Tenggat waktu bisa null
            $table->dateTime('tenggat_waktu')->nullable();

            // Prioritas: 1–5
            $table->unsignedTinyInteger('prioritas')->default(3);

            // Kategori ENUM
            $table->enum('kategori', ['mendadak', 'terjadwal'])->default('terjadwal');

            // ✅ Tambahan kolom jenis_order
            $table->enum('jenis_order', [
                'psb', 
                'survey', 
                'pengecekan error', 
                'request', 
                'lain-lain'
            ])->nullable();

            // Relasi ke user penanggung jawab
            $table->foreignId('penanggung_jawab_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Deskripsi tugas
            $table->text('deskripsi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
