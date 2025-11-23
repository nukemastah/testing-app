<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_rekening',
        'nama_bank',
        'saldo',
    ];

    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class, 'rekening_id');
    }
}
