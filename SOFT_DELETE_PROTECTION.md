# Soft Delete Protection untuk Barang

## Overview
Implementasi ini mencegah error pada laporan ketika barang dihapus dari master barang dengan menggunakan **Soft Delete** dan **Database Trigger Protection**.

## Fitur yang Diimplementasikan

### 1. Soft Delete di Model Barang
- Model `Barang` sudah menggunakan trait `SoftDeletes`
- Ketika barang dihapus, data tidak benar-benar dihapus dari database
- Field `deleted_at` akan terisi timestamp penghapusan
- Data historis tetap tersimpan dan dapat diakses

### 2. Protected Boot Method
File: `app/Models/Barang.php`

Model Barang memiliki method `boot()` yang mencegah penghapusan permanen (force delete) jika barang memiliki transaksi terkait:

```php
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
            throw new \Exception('Cannot permanently delete barang with existing transactions.');
        }
    });
}
```

### 3. WithTrashed() pada Relasi
Semua model yang berelasi dengan Barang sekarang menggunakan `withTrashed()`:

**DetailHjual.php:**
```php
public function barang()
{
    return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
}
```

**PembelianBarang.php:**
```php
public function barang()
{
    return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
}
```

### 4. Update Controller Laporan
Semua controller laporan diupdate untuk menggunakan `withTrashed()`:

#### MutasiStokController
```php
$sales = Penjualan::with(['barang' => function($query) {
    $query->withTrashed();
}])->whereBetween('tanggal', [$startDate, $endDate])->get();
```

#### LaporanPenjualanController
```php
// Untuk list penjualan
$penjualans = Penjualan::with(['barang' => function($query) {
    $query->withTrashed();
}, 'pelanggan'])->whereBetween('tanggal', [$startDate, $endDate])->get();

// Untuk top items
$barang = Barang::withTrashed()->find($r->barang_id);
```

### 5. Helper Function
File: `app/Helpers/BarangHelper.php`

Helper untuk menampilkan nama barang dengan indikator jika sudah dihapus:

```php
// Dengan HTML
BarangHelper::getBarangName($barang);
// Output: "Nama Barang <span style="color: #dc3545;">(Dihapus)</span>"

// Plain text
BarangHelper::getBarangNamePlain($barang);
// Output: "Nama Barang (Dihapus)"

// Cek ketersediaan
BarangHelper::isAvailableForSale($barang);
// Returns: true/false
```

### 6. Database Migration
File: `database/migrations/2025_12_11_055402_add_soft_delete_protection_for_barang_relations.php`

Migration menambahkan:
- Index pada kolom `deleted_at` untuk performa query
- Dokumentasi database tentang soft delete protection

## Cara Kerja

### Skenario 1: User Menghapus Barang dari Master
1. User mengklik tombol hapus di master barang
2. Method `destroy()` di `BarangController` dipanggil
3. `$barang->delete()` hanya men-set `deleted_at` (soft delete)
4. Barang tidak muncul di daftar master barang aktif
5. **Semua transaksi historis tetap utuh**

### Skenario 2: Melihat Laporan dengan Barang yang Dihapus
1. User membuka laporan penjualan/mutasi stok
2. Controller menggunakan `withTrashed()` untuk query
3. Barang yang dihapus tetap muncul dengan label "(Dihapus)"
4. **Tidak ada error "barang not found"**

### Skenario 3: Mencoba Force Delete
1. Jika developer mencoba `$barang->forceDelete()`
2. Boot method akan mengecek apakah ada transaksi terkait
3. Jika ada, akan throw exception
4. **Data historis terlindungi**

## Laporan yang Sudah Dilindungi

✅ **Laporan Mutasi Stok** - Menampilkan barang dihapus dengan label  
✅ **Laporan Penjualan** - Top items dan detail transaksi aman  
✅ **Laporan Kas** - Tidak terpengaruh (menggunakan PembayaranPenjualan)  
✅ **Laporan Piutang** - Tidak terpengaruh (menggunakan NotaHjual)  
✅ **Laporan Hutang** - Tidak terpengaruh (menggunakan NotaHjual)  

## File yang Dimodifikasi

1. `app/Models/Barang.php` - Added boot method and protection
2. `app/Models/DetailHjual.php` - withTrashed() on barang relation
3. `app/Models/PembelianBarang.php` - withTrashed() on barang relation
4. `app/Http/Controllers/MutasiStokController.php` - Updated query
5. `app/Http/Controllers/LaporanPenjualanController.php` - Updated query
6. `app/Helpers/BarangHelper.php` - New helper file
7. `composer.json` - Registered helper file
8. `database/migrations/2025_12_11_055402_add_soft_delete_protection_for_barang_relations.php` - New migration

## Testing

### Test 1: Hapus Barang yang Memiliki Transaksi
```bash
# Di master barang, hapus barang yang sudah pernah dijual
# Expected: Barang soft deleted (moved to trash)
# Expected: Transaksi historis masih bisa dilihat di laporan
```

### Test 2: Lihat Laporan Penjualan
```bash
# Buka laporan penjualan
# Filter periode yang mencakup barang yang dihapus
# Expected: Nama barang muncul dengan label "(Dihapus)"
```

### Test 3: Undo Delete
```bash
# Klik tombol "Undo" di master barang
# Expected: Barang kembali aktif
# Expected: Label "(Dihapus)" hilang dari laporan
```

## Cara Menggunakan Helper di View

### Blade Template
```blade
<!-- Dengan HTML styling -->
{!! \App\Helpers\BarangHelper::getBarangName($penjualan->barang) !!}

<!-- Plain text -->
{{ \App\Helpers\BarangHelper::getBarangNamePlain($penjualan->barang) }}

<!-- Cek ketersediaan -->
@if(\App\Helpers\BarangHelper::isAvailableForSale($barang))
    <button>Beli</button>
@else
    <span class="text-muted">Tidak Tersedia</span>
@endif
```

### Controller
```php
use App\Helpers\BarangHelper;

$namaBarang = BarangHelper::getBarangNamePlain($barang);
$tersedia = BarangHelper::isAvailableForSale($barang);
```

## Keuntungan Implementasi Ini

✅ **Data Integrity** - Transaksi historis tidak akan rusak  
✅ **Audit Trail** - Semua data barang tetap tersimpan  
✅ **User Friendly** - Laporan tetap lengkap tanpa error  
✅ **Undo Support** - Barang dapat dipulihkan kapan saja  
✅ **Performance** - Index pada deleted_at mempercepat query  
✅ **Future Proof** - Mudah untuk menambah laporan baru  

## Notes

- Soft delete menggunakan kolom `deleted_at` yang sudah ada di tabel `barangs`
- Barang yang di-soft delete tidak muncul di query normal (tanpa `withTrashed()`)
- Untuk melihat barang yang dihapus: `Barang::onlyTrashed()->get()`
- Untuk melihat semua termasuk yang dihapus: `Barang::withTrashed()->get()`
- Untuk restore: `$barang->restore()`
- Untuk force delete: `$barang->forceDelete()` (akan dicegah jika ada transaksi)

## Maintenance

Jika menambahkan laporan baru yang menggunakan data barang:
1. Gunakan `withTrashed()` pada eager loading barang
2. Gunakan `BarangHelper::getBarangName()` untuk menampilkan nama
3. Pastikan view menangani null check pada relasi barang

## Troubleshooting

### Error: "Call to undefined method withTrashed()"
- Pastikan model Barang menggunakan trait `SoftDeletes`
- Run `composer dump-autoload`

### Barang Dihapus Tidak Muncul di Laporan
- Pastikan controller menggunakan `withTrashed()` pada query
- Cek apakah relasi di model juga menggunakan `withTrashed()`

### Helper Not Found
- Run `composer dump-autoload`
- Pastikan file ada di `app/Helpers/BarangHelper.php`
- Cek `composer.json` sudah include file helper
