<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    protected $fillable = [
        'barang_id',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_pembayaran',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
