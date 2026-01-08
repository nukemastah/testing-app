<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nama', 'harga', 'harga_jual', 'kuantitas', 'pemasok_id'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'pemasok_id');
    }

    public function pembelians()
    {
        return $this->hasMany(PembelianBarang::class, 'barang_id');
    }

    public function pembayaranPembelians()
    {
        return $this->hasMany(PembayaranPembelian::class, 'barang_id');
    }

    public function detailHjuals()
    {
        return $this->hasMany(DetailHjual::class, 'barang_id');
    }

    /**
     * Relasi ke BarangBatch
     */
    public function batches()
    {
        return $this->hasMany(BarangBatch::class, 'barang_id');
    }

    /**
     * Get total stok dari semua batch yang tersedia
     */
    public function getTotalStokAttribute()
    {
        return $this->batches()->sum('stok_tersedia');
    }

    /**
     * Get batch yang tersedia dengan FIFO/FEFO ordering
     */
    public function getAvailableBatches($useFEFO = true)
    {
        $query = $this->batches()
                     ->available()
                     ->notExpired();
        
        return $useFEFO ? $query->FEFO()->get() : $query->FIFO()->get();
    }

    /**
     * Update kuantitas berdasarkan total batch
     * Method ini untuk sinkronisasi dengan sistem lama
     */
    public function syncKuantitasFromBatches()
    {
        $this->kuantitas = $this->batches()->sum('stok_tersedia');
        $this->save();
    }

    /**
     * Boot method to prevent hard deletion if there are related records
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($barang) {
            // Check if barang has related transactions
            $hasTransactions = $barang->detailHjuals()->exists() || 
                             $barang->pembelians()->exists();
            
            if ($hasTransactions && !$barang->isForceDeleting()) {
                // Allow soft delete to proceed
                return true;
            }
            
            // If force deleting and has transactions, prevent it
            if ($barang->isForceDeleting() && $hasTransactions) {
                throw new \Exception('Cannot permanently delete barang with existing transactions. Use soft delete instead.');
            }
        });
    }
}
