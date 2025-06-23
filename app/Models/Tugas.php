<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'tugas_id',
        'status',
        'tenggat_waktu',
        'prioritas',
        'kategori',
        'penanggung_jawab_id',
        'deskripsi',
        'jenis_order',
    ];

    protected $casts = [
        'tenggat_waktu' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id');
    }

    public static function generateNextId(string $order): string
    {
        $prefixMap = [
            'psb' => 'PSB',
            'survey' => 'SUR',
            'pengecekan error' => 'ERR',
            'request' => 'REQ',
            'lain-lain' => 'LLL',
        ];

        $prefix = $prefixMap[strtolower($order)] ?? 'XXX';

        $last = static::where('tugas_id', 'like', "{$prefix}%")
            ->orderByDesc('tugas_id')
            ->first();

        $lastNumber = 0;

        if ($last && preg_match("/{$prefix}(\d+)/", $last->tugas_id, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    public function getWarnaStatusAttribute(): string
    {
        return match ($this->status) {
            '-' => 'gray',
            'review' => 'warning',
            'proses' => 'primary',
            'segera', 'terlambat' => 'danger',
            'selesai' => 'success',
            'dihentikan', 'dibatalkan' => 'gray',
            default => 'gray',
        };
    }

    public function getLabelOrderAttribute(): string
    {
        return match ($this->jenis_order) {
            'psb' => 'PSB',
            'survey' => 'Survey',
            'pengecekan error' => 'Pengecekan Error',
            'request' => 'Request',
            'lain-lain' => 'Lain-lain',
            default => '-',
        };
    }

    public function getSisaHariAttribute(): string
    {
        $now = now()->startOfDay();
        $tenggat = $this->tenggat_waktu?->copy()->startOfDay();

        if (!$tenggat) {
            return '-';
        }

        $diff = $now->diffInDays($tenggat);

        return "{$diff} hari lagi";
    }

    public function getWarnaSisaHariAttribute(): string
    {
        $now = now()->startOfDay();
        $tenggat = $this->tenggat_waktu?->copy()->startOfDay();

        if (!$tenggat) {
            return 'gray';
        }

        $diff = $now->diffInDays($tenggat, false);

        return match (true) {
            $diff < 1 => 'danger',
            $diff === 1 => 'danger',
            $diff <= 2 => 'warning',
            default => 'success',
        };
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->tugas_id) && !empty($model->jenis_order)) {
                $model->tugas_id = static::generateNextId($model->jenis_order);
            }
        });

        static::saving(function (Tugas $tugas) {
            if ($tugas->tenggat_waktu && $tugas->tenggat_waktu->isPast()) {
                $tugas->tenggat_waktu = now()->startOfDay();
            }
        });
    }
}