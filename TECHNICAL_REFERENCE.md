# Payment System - Technical Reference

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    User Interface (Blade Views)             │
├─────────────────────────────────────────────────────────────┤
│  ├─ penjualan/index.blade.php (Sales with deadline)        │
│  └─ transaksi/pembayaranPenjualan.blade.php (Payments)    │
├─────────────────────────────────────────────────────────────┤
│              HTTP Routes & Controllers                       │
├─────────────────────────────────────────────────────────────┤
│  ├─ PenjualanController (Create sales with deadline)       │
│  └─ PembayaranPenjualanController (CRUD payments)          │
├─────────────────────────────────────────────────────────────┤
│                  Eloquent Models & Logic                     │
├─────────────────────────────────────────────────────────────┤
│  ├─ Penjualan (Sales model)                                │
│  └─ PembayaranPenjualan (Payment model)                    │
├─────────────────────────────────────────────────────────────┤
│              SQLite Database & Migrations                    │
├─────────────────────────────────────────────────────────────┤
│  ├─ penjualans table                                        │
│  └─ pembayaran_penjualans table                            │
├─────────────────────────────────────────────────────────────┤
│                 File Storage System                          │
├─────────────────────────────────────────────────────────────┤
│  └─ storage/app/public/bukti_bayar/ (PDF uploads)          │
└─────────────────────────────────────────────────────────────┘
```

## Database Schema

### penjualans table
```sql
CREATE TABLE penjualans (
    id BIGINT PRIMARY KEY,
    barang_id BIGINT NOT NULL,
    jumlah INT NOT NULL,
    total_harga BIGINT NOT NULL,
    tanggal TIMESTAMP NOT NULL,
    pelanggan_id BIGINT NULL,
    tenggat_pembayaran DATE NULL,              -- NEW
    status_pembayaran VARCHAR(20) DEFAULT 'belum bayar',  -- NEW
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (barang_id) REFERENCES barangs(id),
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggans(id)
);
```

### pembayaran_penjualans table
```sql
CREATE TABLE pembayaran_penjualans (
    id BIGINT PRIMARY KEY,
    penjualan_id BIGINT NOT NULL,              -- NEW
    jumlah_bayar INT NOT NULL,                 -- NEW
    bukti_bayar VARCHAR(255) NULL,             -- NEW
    tanggal_pembayaran DATE NOT NULL,          -- NEW
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualans(id) 
        ON DELETE CASCADE
);
```

## Model Relationships

### Penjualan Model
```php
class Penjualan extends Model {
    // Existing relationships
    public function barang() {
        return $this->belongsTo(Barang::class);
    }
    
    public function pelanggan() {
        return $this->belongsTo(Pelanggan::class);
    }
    
    // New relationship
    public function pembayarans() {
        return $this->hasMany(PembayaranPenjualan::class, 'penjualan_id');
    }
}
```

### PembayaranPenjualan Model
```php
class PembayaranPenjualan extends Model {
    protected $fillable = [
        'penjualan_id',
        'jumlah_bayar',
        'bukti_bayar',
        'tanggal_pembayaran',
    ];
    
    public function penjualan() {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }
}
```

## API Reference

### PembayaranPenjualanController Methods

#### index()
```
GET /transaksi/pembayaran-penjualan
Returns: View with all payments and available sales

Query:
SELECT pembayaran_penjualans.*
FROM pembayaran_penjualans
LEFT JOIN penjualans ON pembayaran_penjualans.penjualan_id = penjualans.id
LEFT JOIN pelanggans ON penjualans.pelanggan_id = pelanggans.id
ORDER BY tanggal_pembayaran DESC

Response: Blade view with:
  - $pembayarans: Collection of payment records
  - $penjualans: Collection of all sales for dropdown
```

#### store()
```
POST /pembayaran-penjualan
Input Validation:
  - penjualan_id: required|exists:penjualans,id
  - jumlah_bayar: required|integer|min:1
  - bukti_bayar: nullable|file|mimes:pdf|max:5120
  - tanggal_pembayaran: nullable|date

Process:
  1. Validate input
  2. Find penjualan record
  3. Handle file upload (if provided)
  4. Create PembayaranPenjualan record
  5. Call updatePaymentStatus()
  6. Redirect with success message

File Handling:
  - Filename: {timestamp}_{customer_name}.pdf
  - Location: storage/app/public/bukti_bayar/
  - Storage disk: public
  - Accessible via: Storage::url($pembayaran->bukti_bayar)

Response: Redirect to pembayaran-penjualan.index with success message
```

#### destroy()
```
DELETE /pembayaran-penjualan/{pembayaranPenjualan}
Input:
  - pembayaranPenjualan: PembayaranPenjualan model instance

Process:
  1. Get parent penjualan
  2. Delete file from storage (if exists)
  3. Delete database record
  4. Call updatePaymentStatus()
  5. Redirect with success message

Response: Redirect to pembayaran-penjualan.index with success message
```

#### updatePaymentStatus()
```
Private Helper Method
Input:
  - penjualan: Penjualan model instance

Logic:
  // Calculate total paid
  $totalPaid = $penjualan->pembayarans()->sum('jumlah_bayar');
  $totalHarga = $penjualan->total_harga;
  
  // Determine status
  if ($totalPaid >= $totalHarga) {
      $status = 'lunas';
  } elseif ($totalPaid > 0) {
      $status = 'kurang bayar';
  } else {
      $status = 'belum bayar';
  }
  
  // Check if late
  if ($penjualan->tenggat_pembayaran && $totalPaid > 0 && $totalPaid < $totalHarga) {
      if (now()->toDateString() > $penjualan->tenggat_pembayaran) {
          $status = 'telat bayar';
      }
  }
  
  // Update penjualan
  $penjualan->update(['status_pembayaran' => $status]);

Response: Updates database record
```

## PenjualanController Updates

### store() Method
```php
public function store(Request $request)
{
    // Validate input (including new tenggat_pembayaran)
    $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'jumlah' => 'required|integer|min:1',
        'harga_jual' => 'nullable|numeric|min:0',
        'pelanggan_id' => 'nullable|exists:pelanggans,id',
        'tenggat_pembayaran' => 'nullable|date',  // NEW
    ]);
    
    // Create penjualan (includes new fields)
    $penjualan = Penjualan::create([
        'barang_id' => $barang->id,
        'jumlah' => $request->jumlah,
        'total_harga' => $totalHarga,
        'tanggal' => now(),
        'pelanggan_id' => $request->pelanggan_id ?? null,
        'tenggat_pembayaran' => $request->tenggat_pembayaran ?? null,  // NEW
        'status_pembayaran' => 'belum bayar',  // NEW - Always starts unpaid
    ]);
    
    // Update barang stock
    $barang->kuantitas -= $request->jumlah;
    $barang->save();
    
    return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil.');
}
```

## Validation Rules

### Payment Creation
```
POST /pembayaran-penjualan
penjualan_id:
  - required
  - must exist in penjualans table
  - foreign key validation

jumlah_bayar:
  - required
  - must be integer
  - minimum 1 (no zero or negative payments)

bukti_bayar:
  - optional
  - if provided, must be PDF file
  - mime type: application/pdf
  - max size: 5120 KB (5MB)

tanggal_pembayaran:
  - optional
  - if provided, must be valid date
  - defaults to current date
  - format: YYYY-MM-DD
```

### Sales Creation
```
POST /penjualan
barang_id:
  - required
  - must exist in barangs table

jumlah:
  - required
  - must be integer
  - minimum 1

harga_jual:
  - optional
  - if provided, must be numeric
  - minimum 0

pelanggan_id:
  - optional
  - if provided, must exist in pelanggans table

tenggat_pembayaran:
  - optional
  - if provided, must be valid date
  - format: YYYY-MM-DD
```

## Status Calculation Algorithm

```javascript
function calculatePaymentStatus(penjualan) {
  // Step 1: Calculate total paid
  const totalPaid = penjualan.pembayarans
    .reduce((sum, payment) => sum + payment.jumlah_bayar, 0);
  
  const totalHarga = penjualan.total_harga;
  
  // Step 2: Determine base status
  let status;
  if (totalPaid >= totalHarga) {
    status = 'lunas';  // Fully paid
  } else if (totalPaid > 0) {
    status = 'kurang bayar';  // Partially paid (default)
  } else {
    status = 'belum bayar';  // Not paid
  }
  
  // Step 3: Check if late (only applies to partial payments)
  if (penjualan.tenggat_pembayaran && 
      totalPaid > 0 && 
      totalPaid < totalHarga) {
    
    const today = new Date();
    const deadline = new Date(penjualan.tenggat_pembayaran);
    
    if (today > deadline) {
      status = 'telat bayar';  // Late payment
    }
  }
  
  return status;
}
```

## File Upload Process

```
1. User selects PDF file
2. Browser sends multipart form data

3. Laravel processes:
   - Validates MIME type (PDF only)
   - Validates file size (max 5MB)
   
4. If valid:
   - Generate filename: {timestamp}_{customer_name}.pdf
   - Store in: storage/app/public/bukti_bayar/
   - Save path in: pembayaran_penjualans.bukti_bayar
   - Record created in database
   
5. If invalid:
   - Show validation error
   - Return to form with error message
   
6. File access:
   - Public URL: Storage::url($pembayaran->bukti_bayar)
   - Physical path: storage/app/public/bukti_bayar/{filename}
   - Accessible via web at: /storage/bukti_bayar/{filename}
```

## Error Handling

### Validation Errors
```
Form validation fails → Show error message → User returns to form
- Invalid penjualan_id → "Transaksi tidak ditemukan"
- Invalid amount → "Jumlah bayar harus angka positif"
- Invalid file → "File harus PDF dan maksimal 5MB"
```

### Business Logic Errors
```
File operations fail → Log error → Show generic message → Redirect
- File upload fails → Payment record not created
- File deletion fails → Log error but complete deletion
- Status update fails → Log error but allow payment record
```

### Database Errors
```
Foreign key violations → Handled by Eloquent relationships
- Delete payment → Cascade properly handled
- Orphaned records → Prevented by NOT NULL FK constraint
```

## Performance Considerations

### Query Optimization
```php
// Index the queries
Penjualan::with(['barang', 'pelanggan', 'pembayarans'])
    ->latest()
    ->get();

PembayaranPenjualan::with('penjualan.pelanggan')
    ->orderBy('tanggal_pembayaran', 'desc')
    ->get();
```

### Database Indexes
```sql
-- Recommended indexes (not explicitly created but beneficial)
CREATE INDEX idx_pembayaran_penjualan_id 
    ON pembayaran_penjualans(penjualan_id);

CREATE INDEX idx_pembayaran_tanggal 
    ON pembayaran_penjualans(tanggal_pembayaran);

CREATE INDEX idx_penjualan_status 
    ON penjualans(status_pembayaran);

CREATE INDEX idx_penjualan_tenggat 
    ON penjualans(tenggat_pembayaran);
```

### N+1 Query Prevention
```php
// GOOD: Eager load relationships
$pembayarans = PembayaranPenjualan::with('penjualan.pelanggan')
    ->orderBy('tanggal_pembayaran', 'desc')
    ->get();

// BAD: Lazy load relationships (causes N+1)
$pembayarans = PembayaranPenjualan::all();
foreach ($pembayarans as $p) {
    echo $p->penjualan->pelanggan->nama;  // Additional query each iteration
}
```

## Security Measures

### CSRF Protection
```html
<!-- All forms include @csrf token -->
<form method="POST" action="{{ route('pembayaran-penjualan.store') }}">
    @csrf
    <!-- form fields -->
</form>
```

### File Upload Security
```php
// Validate file type
'bukti_bayar' => 'nullable|file|mimes:pdf|max:5120'

// Store outside public web root
// File path: storage/app/public/bukti_bayar/
// NOT: public/uploads/
```

### Input Sanitization
```php
// All inputs validated before use
$validated = $request->validate([
    'penjualan_id' => 'required|exists:penjualans,id',
    'jumlah_bayar' => 'required|integer|min:1',
]);

// Database prepared statements (Eloquent)
$penjualan = Penjualan::findOrFail($validated['penjualan_id']);
```

### Authorization
```php
// Routes protected by middleware
Route::middleware(['auth'])->group(function () {
    // Payment routes here
    // Only authenticated users can access
});
```

## Testing Checklist

### Unit Tests (Example)
```php
// Test status calculation
$penjualan = Penjualan::create([...]);
PembayaranPenjualan::create(['penjualan_id' => $penjualan->id, 'jumlah_bayar' => 100000]);
assertEquals('kurang bayar', $penjualan->status_pembayaran);

// Test file upload
$file = UploadedFile::fake()->create('bukti.pdf', 1024);
$response = $this->post('/pembayaran-penjualan', [
    'penjualan_id' => 1,
    'jumlah_bayar' => 100000,
    'bukti_bayar' => $file
]);
assertEquals(201, $response->status());
```

## Deployment Checklist

- [ ] Run migrations: `php artisan migrate --force`
- [ ] Create storage symlink: `php artisan storage:link`
- [ ] Set proper permissions: `chmod -R 775 storage/`
- [ ] Set file ownership: `chown -R www-data:www-data storage/`
- [ ] Verify database connection
- [ ] Clear application cache: `php artisan cache:clear`
- [ ] Optimize autoloader: `composer dump-autoload -o`
- [ ] Test payment form
- [ ] Test file upload
- [ ] Verify status calculations

---

**Document Version**: 1.0
**Last Updated**: 2025-11-24
**Status**: Complete ✅
