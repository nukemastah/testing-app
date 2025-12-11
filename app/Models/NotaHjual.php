<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaHjual extends Model
{
    protected $table = 'nota_hjuals';
    protected $primaryKey = 'no_nota';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'no_nota',
        'tanggal',
        'pelanggan_id',
        'total_item',
        'total_harga',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function details()
    {
        return $this->hasMany(DetailHjual::class, 'no_nota', 'no_nota');
    }
    
    public function pembayarans()
    {
        return $this->hasMany(PembayaranPenjualan::class, 'no_nota', 'no_nota');
    }
}
