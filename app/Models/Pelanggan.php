<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pelanggan',
        'nama_pelanggan',
        'alamat',
        'rekening_id',
    ];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'pelanggan_id');
    }
}
