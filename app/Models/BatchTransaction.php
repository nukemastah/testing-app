<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchTransaction extends Model
{
    protected $fillable = [
        'barang_batch_id',
        'penjualan_id',
        'tipe',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    /**
     * Relasi ke BarangBatch
     */
    public function barangBatch()
    {
        return $this->belongsTo(BarangBatch::class);
    }

    /**
     * Relasi ke Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }
}
