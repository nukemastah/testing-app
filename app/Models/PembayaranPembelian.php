<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    protected $fillable = [
        'pembelian_id',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];

    public function pembelian()
    {
        return $this->belongsTo(PembelianBarang::class, 'pembelian_id');
    }
}
