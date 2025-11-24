<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model
{
    protected $fillable = [
        'penjualan_id',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_pembayaran',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }
}
