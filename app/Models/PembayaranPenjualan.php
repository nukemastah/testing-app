<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPenjualan extends Model
{
    protected $fillable = [
        'no_nota',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_pembayaran',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
    ];

    public function notaHjual()
    {
        return $this->belongsTo(NotaHjual::class, 'no_nota', 'no_nota');
    }
    
    // Backward compatibility alias
    public function penjualan()
    {
        return $this->notaHjual();
    }
}
