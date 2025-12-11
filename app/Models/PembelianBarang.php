<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBarang extends Model
{
    protected $table = 'pembelian_barangs';

    protected $fillable = [
        'barang_id',
        'pemasok_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'tanggal_pembelian',
        'status_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
    }

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id');
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranPembelian::class, 'pembelian_id');
    }
}
