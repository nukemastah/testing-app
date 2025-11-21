<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasok extends Model
{
    protected $fillable = ['kode_pemasok', 'nama_pemasok', 'alamat_pemasok'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_pemasok)) {
                $lastPemasok = static::latest('id')->first();
                $lastNumber = 0;

                if ($lastPemasok && preg_match('/S-(\d+)/', $lastPemasok->kode_pemasok, $matches)) {
                    $lastNumber = (int)$matches[1];
                }

                $newNumber = $lastNumber + 1;
                $model->kode_pemasok = 'S-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
