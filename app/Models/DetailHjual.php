<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailHjual extends Model
{
    protected $table = 'detail_hjuals';

    protected $fillable = [
        'no_nota',
        'barang_id',
        'quantity',
        'harga_jual',
        'subtotal',
    ];

    public function notaHjual()
    {
        return $this->belongsTo(NotaHjual::class, 'no_nota', 'no_nota');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Auto-calculate subtotal jika belum ada
            if (empty($model->subtotal)) {
                $model->subtotal = $model->quantity * $model->harga_jual;
            }
        });

        static::updating(function ($model) {
            // Auto-calculate subtotal saat update
            if ($model->isDirty(['quantity', 'harga_jual'])) {
                $model->subtotal = $model->quantity * $model->harga_jual;
            }
        });
    }
}
