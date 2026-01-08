<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangBatch extends Model
{
    protected $fillable = [
        'barang_id',
        'pembelian_barang_id',
        'batch_number',
        'harga_beli',
        'stok_awal',
        'stok_tersedia',
        'tanggal_masuk',
        'tanggal_kadaluarsa',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'harga_beli' => 'integer',
        'stok_awal' => 'integer',
        'stok_tersedia' => 'integer',
    ];

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Relasi ke PembelianBarang
     */
    public function pembelianBarang()
    {
        return $this->belongsTo(PembelianBarang::class);
    }

    /**
     * Relasi ke transaksi batch
     */
    public function transactions()
    {
        return $this->hasMany(BatchTransaction::class, 'barang_batch_id');
    }

    /**
     * Scope untuk batch yang masih tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('stok_tersedia', '>', 0);
    }

    /**
     * Scope untuk batch yang belum kadaluarsa
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('tanggal_kadaluarsa')
              ->orWhere('tanggal_kadaluarsa', '>=', Carbon::now()->toDateString());
        });
    }

    /**
     * Scope untuk ordering FIFO (First In First Out)
     */
    public function scopeFIFO($query)
    {
        return $query->orderBy('tanggal_masuk', 'asc')
                    ->orderBy('id', 'asc');
    }

    /**
     * Scope untuk ordering FEFO (First Expired First Out)
     */
    public function scopeFEFO($query)
    {
        return $query->orderByRaw('CASE WHEN tanggal_kadaluarsa IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('tanggal_kadaluarsa', 'asc')
                    ->orderBy('tanggal_masuk', 'asc')
                    ->orderBy('id', 'asc');
    }

    /**
     * Check apakah batch sudah kadaluarsa
     */
    public function isExpired()
    {
        if (!$this->tanggal_kadaluarsa) {
            return false;
        }
        return Carbon::parse($this->tanggal_kadaluarsa)->isPast();
    }

    /**
     * Get hari tersisa sebelum kadaluarsa
     */
    public function daysUntilExpiry()
    {
        if (!$this->tanggal_kadaluarsa) {
            return null;
        }
        return Carbon::now()->diffInDays($this->tanggal_kadaluarsa, false);
    }

    /**
     * Generate batch number otomatis
     */
    public static function generateBatchNumber()
    {
        return 'BTH-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
