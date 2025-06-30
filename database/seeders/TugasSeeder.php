<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tugas; // Import model Tugas
use App\Models\User; // Import model User jika perlu relasi ke pelanggan/penanggung jawab

class TugasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('id_ID'); // Atur faker untuk bahasa Indonesia
        $users = User::all()->pluck('id'); // Ambil ID user yang ada

        for ($i = 0; $i < 10; $i++) { // Buat 10 tugas dummy
            Tugas::create([
                'tugas_id' => 'TASK-' . $faker->unique()->randomNumber(5),
                'judul' => $faker->sentence(mt_rand(3, 8)),
                'pelanggan_id' => $users->random(), // Pilih pelanggan acak
                'status' => $faker->randomElement(['review', 'proses', 'segera', 'terlambat', 'selesai']),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'), // Tenggat waktu 1 bulan ke depan
                'prioritas' => $faker->numberBetween(1, 4),
                'kategori' => $faker->randomElement(['mendadak', 'terjadwal']),
                'jenis_order' => $faker->randomElement(['psb', 'survey', 'pengecekan error', 'request', 'lain-lain']),
                'penanggung_jawab_id' => $users->random(), // Pilih penanggung jawab acak
                'deskripsi' => $faker->paragraph(mt_rand(1, 3)),
            ]);
        }
    }
}