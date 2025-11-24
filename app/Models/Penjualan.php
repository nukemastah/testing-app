<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'jumlah',
        'total_harga',
        'tanggal',
        'pelanggan_id',
        'tenggat_pembayaran',
        'status_pembayaran',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranPenjualan::class, 'penjualan_id');
    }

    protected $casts = [
        'tanggal' => 'date',
    ];
}
