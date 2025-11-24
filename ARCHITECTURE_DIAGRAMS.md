# Payment System Architecture Diagram

## System Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                          USER INTERFACE                             │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐ │
│  │ Penjualan Index  │  │ Pembayaran Form  │  │ Pembayaran Table │ │
│  │  (Sales List)    │  │  (Record Payment)│  │   (View All)     │ │
│  ├──────────────────┤  ├──────────────────┤  ├──────────────────┤ │
│  │ Shows:           │  │ Fields:          │  │ Shows:           │ │
│  │ - Barang         │  │ - Penjualan ID   │  │ - Transaction #  │ │
│  │ - Qty            │  │ - Amount         │  │ - Customer       │ │
│  │ - Total Harga    │  │ - Date           │  │ - Amount         │ │
│  │ - Pelanggan      │  │ - PDF Upload     │  │ - PDF Link       │ │
│  │ - Tenggat Bayar  │  │                  │  │ - Delete Button  │ │
│  │ - Status Badge   │  │ Actions:         │  │                  │ │
│  │   (Color Coded)  │  │ - Simpan Bayaran │  │ Actions:         │ │
│  │                  │  │ - Bersihkan      │  │ - Hapus          │ │
│  └──────────────────┘  └──────────────────┘  └──────────────────┘ │
│         △                    │                      △               │
└─────────────────────────────────────────────────────────────────────┘
          │                    │                      │
          │ GET               │ POST                │ DELETE
          │ /penjualan        │ /pembayaran-penjualan  /pembayaran-penjualan/{id}
          │                   │                      │
          ├───────────────────┴──────────────────────┤
          │                                          │
┌─────────────────────────────────────────────────────────────────────┐
│                    ROUTE LAYER (Laravel)                            │
├─────────────────────────────────────────────────────────────────────┤
│  routes/web.php - Route definitions and middleware                 │
└─────────────────────────────────────────────────────────────────────┘
          │                                          │
          │ routes to                                │
          ├──────────────────┬──────────────────────┤
          │                  │                      │
┌──────────────────┐ ┌────────────────────┐ ┌──────────────────┐
│  Penjualan       │ │ Pembayaran         │ │ Pembayaran       │
│  Controller      │ │ Penjualan          │ │ Penjualan        │
│                  │ │ Controller         │ │ Controller       │
├──────────────────┤ ├────────────────────┤ ├──────────────────┤
│ Methods:         │ │ Methods:           │ │ Inherited:       │
│ - index()        │ │ - index()          │ │ (from same)      │
│ - store()        │ │ - store()          │ │                  │
│ - destroy()      │ │ - destroy()        │ │                  │
│                  │ │ - updatePayment    │ │                  │
│ Actions:         │ │   Status()         │ │                  │
│ - Load data      │ │                    │ │                  │
│ - Validate       │ │ Actions:           │ │                  │
│ - Save to DB     │ │ - Validate input   │ │                  │
│ - Load payment   │ │ - Handle file      │ │                  │
│   deadline       │ │ - Create record    │ │                  │
│ - Set initial    │ │ - Calc status      │ │                  │
│   status         │ │ - Update penjualan │ │                  │
└──────────────────┘ └────────────────────┘ └──────────────────┘
          │                  │                      │
          │ query            │ save                 │ query
          │                  │                      │
┌─────────────────────────────────────────────────────────────────────┐
│               ELOQUENT ORM LAYER (Models)                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────┐  ┌──────────────────────┐  ┌──────────────┐ │
│  │   Penjualan      │  │ PembayaranPenjualan  │  │ Pelanggan    │ │
│  │   (Sales)        │  │   (Payments)         │  │              │ │
│  ├──────────────────┤  ├──────────────────────┤  ├──────────────┤ │
│  │ Properties:      │  │ Properties:          │  │ Properties:  │ │
│  │ - id             │  │ - id                 │  │ - id         │ │
│  │ - barang_id      │  │ - penjualan_id (FK)  │  │ - nama...    │ │
│  │ - jumlah         │  │ - jumlah_bayar       │  │ - alamat...  │ │
│  │ - total_harga    │  │ - bukti_bayar        │  │              │ │
│  │ - tanggal        │  │ - tanggal_pembayaran │  │              │ │
│  │ - pelanggan_id   │  │ - created_at         │  │              │
│  │ - tenggat_...    │  │ - updated_at         │  │              │
│  │ - status_...     │  │                      │  │              │
│  │                  │  │ Relationships:       │  │              │
│  │ Relationships:   │  │ - belongsTo          │  │              │
│  │ - hasMany()      │  │   (Penjualan)        │  │              │
│  │   pembayarans    │  │                      │  │              │
│  │ - belongsTo()    │  │                      │  │              │
│  │   pelanggan      │  │                      │  │              │
│  │ - belongsTo()    │  │                      │  │              │
│  │   barang         │  │                      │  │              │
│  └──────────────────┘  └──────────────────────┘  └──────────────┘
│         △                      △                        △          │
│         │                      │                        │          │
│         └──────────────────────┼────────────────────────┘          │
│                                │                                   │
│                    Has-Many    │    Belongs-To                     │
│                    One-To-Many │    Many-To-One                    │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
          │                      │
          │ INSERT/UPDATE        │ INSERT/UPDATE/DELETE
          │ SELECT               │ SELECT
          │                      │
┌─────────────────────────────────────────────────────────────────────┐
│                   DATABASE LAYER (SQLite)                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌────────────────────────────────┐  ┌──────────────────────────┐ │
│  │  penjualans table              │  │  pembayaran_penjualans   │ │
│  │  (Sales Records)               │  │  (Payment Records)       │ │
│  ├────────────────────────────────┤  ├──────────────────────────┤ │
│  │ id (PK)                        │  │ id (PK)                  │ │
│  │ barang_id (FK)                 │  │ penjualan_id (FK)        │ │
│  │ jumlah                         │  │ jumlah_bayar             │ │
│  │ total_harga                    │  │ bukti_bayar              │ │
│  │ tanggal                        │  │ tanggal_pembayaran       │ │
│  │ pelanggan_id (FK)              │  │ created_at               │ │
│  │ tenggat_pembayaran [NEW]       │  │ updated_at               │ │
│  │ status_pembayaran [NEW]        │  │                          │ │
│  │ created_at                     │  │ [CASCADE DELETE]          │ │
│  │ updated_at                     │  │ on penjualan DELETE      │ │
│  └────────────────────────────────┘  └──────────────────────────┘ │
│         △                                    △                    │ │
│         │                                    │                    │ │
│         └────────────────────────────────────┘                    │ │
│              One penjualan                                        │ │
│              can have many                                        │ │
│              pembayaran_penjualans                                │ │
│                                                                     │ │
└─────────────────────────────────────────────────────────────────────┘
          │
          │ stored files
          │
┌─────────────────────────────────────────────────────────────────────┐
│                    FILE STORAGE LAYER                              │
├─────────────────────────────────────────────────────────────────────┤
│  storage/app/public/bukti_bayar/                                   │
│  ├── 1732430696_budi_santoso.pdf                                   │
│  ├── 1732430712_siti_nurhaliza.pdf                                 │
│  └── ...more PDF files...                                          │
│                                                                     │
│  Naming: {timestamp}_{customer_name}.pdf                           │
│  Access: Storage::url() → /storage/bukti_bayar/filename.pdf        │
│  Security: Validated mime type, size limited, cascade delete       │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow Diagram

```
RECORDING A PAYMENT:

User Input
   ↓
┌─ Validation ─┐
│ - Check ID   │
│ - Check Amt  │
│ - Check File │
└──────────────┘
   │ ✓ Pass
   ↓
┌──── File Upload (if provided) ────┐
│ 1. Validate MIME type (PDF only)   │
│ 2. Validate size (max 5MB)         │
│ 3. Generate filename with timestamp│
│ 4. Store in public disk            │
└────────────────────────────────────┘
   ↓
┌──── Create Payment Record ────┐
│ INSERT into pembayaran_penjualans:│
│ - penjualan_id                │
│ - jumlah_bayar                │
│ - bukti_bayar (file path)     │
│ - tanggal_pembayaran (today)  │
└────────────────────────────────┘
   ↓
┌──── Update Payment Status ────┐
│ 1. Sum all payments for sale  │
│ 2. Compare to total_harga:    │
│    IF total_paid >= total     │
│      status = 'lunas'         │
│    ELSEIF total_paid > 0      │
│      IF past deadline         │
│        status = 'telat bayar' │
│      ELSE                     │
│        status = 'kurang bayar'│
│    ELSE                       │
│      status = 'belum bayar'   │
│ 3. UPDATE penjualan table     │
└────────────────────────────────┘
   ↓
Display Success Message


VIEWING PAYMENT STATUS:

User Views Sales List
   ↓
Controller queries:
  SELECT * FROM penjualans
  WITH EAGER LOADING:
  - WITH barang
  - WITH pelanggan
  - WITH pembayarans (relationships)
   ↓
Display in Table with:
  - Status badge (color coded)
  - Deadline date
  - Total amount
  - Pelanggan name
```

## Status Calculation Logic Flow

```
START: Calculate Status for a Penjualan

┌─────────────────────────────────────┐
│ totalPaid = SUM(pembayaran_penjualan│
│            .jumlah_bayar)           │
│ totalHarga = penjualan.total_harga  │
└─────────────────────────────────────┘
          │
          ↓
    ┌─────────────┐
    │ Lunas?      │
    │ totalPaid   │
    │   >=        │
    │ totalHarga  │
    └─────────────┘
      │       │
     YES     NO
      │       │
      │       ↓
      │   ┌─────────────────┐
      │   │ Partial Payment?│
      │   │ totalPaid > 0   │
      │   └─────────────────┘
      │        │       │
      │       YES     NO (return BELUM BAYAR)
      │        │
      │        ↓
      │   ┌─────────────────────┐
      │   │ Late Payment?       │
      │   │ IF tenggat_pembayaran
      │   │   IS NOT NULL AND   │
      │   │   today >           │
      │   │   tenggat_pembayaran│
      │   └─────────────────────┘
      │        │       │
      │       YES     NO (return KURANG BAYAR)
      │        │
      │        ↓
      │   return TELAT BAYAR
      │
      ↓
  return LUNAS

END: Update penjualan.status_pembayaran
```

## Component Interaction Diagram

```
                    ┌─────────────────────┐
                    │  Web Browser        │
                    │  (User Interface)   │
                    └──────────┬──────────┘
                               │
                    ┌──────────┴──────────┐
                    │                     │
              GET Request           POST/DELETE Request
              (View Pages)          (Save/Delete Data)
                    │                     │
                    ↓                     ↓
         ┌────────────────────┐ ┌──────────────────────┐
         │ PenjualanController│ │PembayaranPenjualan   │
         │ (index method)     │ │Controller            │
         │                    │ │(store/destroy)       │
         │ - Get all sales    │ │                      │
         │ - Load options     │ │ - Validate input     │
         │ - Render view      │ │ - Handle file upload │
         │                    │ │ - Save to DB         │
         │ Returns: View      │ │ - Calculate status   │
         │                    │ │ - Redirect to index  │
         └────────────────────┘ └──────────────────────┘
                    │                     │
                    │ query               │ query
                    ├─────────┬───────────┤
                    │         │           │
                    ↓         ↓           ↓
         ┌────────────────┐ ┌──────────────────────┐
         │   Penjualan    │ │PembayaranPenjualan   │
         │    Model       │ │   Model              │
         │                │ │                      │
         │ - with()       │ │ - with('penjualan')  │
         │ - get()        │ │ - create()           │
         │ - find()       │ │ - delete()           │
         │ - update()     │ │ - sum('jumlah_bayar')│
         │                │ │                      │
         └────────────────┘ └──────────────────────┘
                    │                     │
                    │ INSERT/UPDATE/      │ INSERT/UPDATE/
                    │ SELECT query        │ DELETE query
                    └─────────┬───────────┘
                              │
                    ┌─────────┴──────────┐
                    │                    │
                    ↓                    ↓
              ┌──────────┐         ┌─────────────┐
              │penjualans│         │pembayaran_  │
              │ table    │         │penjualans   │
              │          │         │ table       │
              │(SQLite)  │         │(SQLite)     │
              └──────────┘         └─────────────┘
                    │                    │
           ┌────────┴────────┐           │
           │                 │           │
     Updates with    Updates with   Relates to
     payment status  payment record  penjualan
           │                 │           │
           └─────────────────┴───────────┘


                     ┌──────────────────┐
                     │  File Storage    │
                     │  (Public Disk)   │
                     │                  │
                     │bukti_bayar/      │
                     │├─payment1.pdf    │
                     │├─payment2.pdf    │
                     │└─...             │
                     └──────────────────┘
                             △
                             │
                      Stores/Retrieves
                       PDF Files
                             │
                    ┌────────┴────────┐
                    │                 │
                 Upload          Download
               (on store)       (on view)
                    │                 │
                    └─────────────────┘
```

## Security & Validation Flow

```
Payment Form Submission
         │
         ↓
┌─────────────────────────────────┐
│ CSRF Token Verification         │
│ (Laravel middleware)            │
│ - Prevent cross-site attacks    │
└─────────────────────────────────┘
         │ ✓
         ↓
┌─────────────────────────────────┐
│ Input Validation                │
│ - penjualan_id exists check     │
│ - jumlah_bayar is positive int  │
│ - tanggal_pembayaran is date    │
│ - bukti_bayar mime type check   │
└─────────────────────────────────┘
         │ ✓
         ↓
┌─────────────────────────────────┐
│ File Upload Validation          │
│ (if file provided)              │
│ - MIME type: application/pdf    │
│ - File size: <= 5MB             │
│ - Unique filename generation    │
└─────────────────────────────────┘
         │ ✓
         ↓
┌─────────────────────────────────┐
│ Data Storage                    │
│ - Safe filename used            │
│ - Stored outside web root       │
│ - DB record with file path      │
│ - No direct execute permission  │
└─────────────────────────────────┘
         │ ✓
         ↓
┌─────────────────────────────────┐
│ Database Integrity              │
│ - Foreign key constraints       │
│ - Cascade delete on removal     │
│ - Transaction safety            │
└─────────────────────────────────┘
         │ ✓
         ↓
   Success Message
```

---

**Architecture Documentation Version**: 1.0
**Created**: 2025-11-24
**System**: Payment System - Laravel 11
