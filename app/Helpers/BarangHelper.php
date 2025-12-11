<?php

namespace App\Helpers;

use App\Models\Barang;

class BarangHelper
{
    /**
     * Get barang name with deleted indicator
     * 
     * @param Barang|null $barang
     * @param string $defaultName
     * @return string
     */
    public static function getBarangName($barang, $defaultName = 'Barang Tidak Ditemukan')
    {
        if (!$barang) {
            return $defaultName;
        }
        
        $name = $barang->nama;
        
        if ($barang->trashed()) {
            $name .= ' <span style="color: #dc3545; font-size: 0.85em;">(Dihapus)</span>';
        }
        
        return $name;
    }
    
    /**
     * Get plain barang name with deleted indicator (no HTML)
     * 
     * @param Barang|null $barang
     * @param string $defaultName
     * @return string
     */
    public static function getBarangNamePlain($barang, $defaultName = 'Barang Tidak Ditemukan')
    {
        if (!$barang) {
            return $defaultName;
        }
        
        $name = $barang->nama;
        
        if ($barang->trashed()) {
            $name .= ' (Dihapus)';
        }
        
        return $name;
    }
    
    /**
     * Check if barang exists and can be sold
     * 
     * @param Barang|null $barang
     * @return bool
     */
    public static function isAvailableForSale($barang)
    {
        if (!$barang || $barang->trashed()) {
            return false;
        }
        
        return $barang->kuantitas > 0;
    }
}
