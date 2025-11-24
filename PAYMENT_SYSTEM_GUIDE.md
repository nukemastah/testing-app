# Payment System Quick Reference Guide

## 🎯 Quick Start

### Accessing Payment System
1. **Main Menu**: Transaksi → Pembayaran Penjualan
2. **URL**: `/transaksi/pembayaran-penjualan`

## 📝 How to Record a Payment

### Step-by-Step
1. Navigate to **Pembayaran Penjualan** from menu
2. Fill in the form:
   - **Nomor Transaksi Penjualan**: Select from dropdown
   - **Jumlah Bayar**: Enter amount in Rp (numbers only)
   - **Tanggal Pembayaran**: Optional (defaults to today)
   - **Bukti Pembayaran**: Optional PDF file (max 5MB)
3. Click **Simpan Pembayaran** button
4. See success message and payment appears in table

### Example
- Select: `#00042 - Budi Santoso (Rp500.000)`
- Amount: `250000`
- Date: Leave blank (uses today)
- File: Upload receipt PDF
- Result: Payment recorded, status updates

## 📊 Understanding Payment Status

### Status Badges

| Status | Color | Meaning |
|--------|-------|---------|
| ✓ LUNAS | Green | Fully paid - customer paid in full |
| ⚠ KURANG BAYAR | Yellow | Partially paid - customer still owes |
| ⛔ TELAT BAYAR | Red | Late payment - past deadline |
| ○ BELUM BAYAR | Blue | Not paid - no payments received |

### Status Rules

**LUNAS (Fully Paid)**
- Total paid = Total sale amount
- Appears when: Payment(s) reach or exceed total

**KURANG BAYAR (Underpaid)**
- 0 < Total paid < Total sale amount
- Appears when: Partial payment made but not complete

**BELUM BAYAR (Unpaid)**
- Total paid = 0
- Appears when: No payments recorded yet
- Default status for new sales

**TELAT BAYAR (Late Payment)**
- Payment received AFTER deadline
- Total paid is still less than total
- Appears when: Date now > Tenggat Bayar AND payment < total

## 📅 Payment Deadline

### Setting a Deadline
1. When creating a new sale:
   - Go to **Penjualan** page
   - Enter a date in **Tenggat Pembayaran** field
   - This is the expected payment date

### Using Deadlines
- Optional field - leave blank if no deadline
- Helps track late payments
- System marks as "TELAT BAYAR" if payment comes after deadline
- For accounting and follow-up reminders

## 💾 Managing Payments

### Viewing Payments
1. Go to **Pembayaran Penjualan**
2. See all payments in table below form
3. Sorted by most recent first

### Downloaded Payment Proof
- If PDF was uploaded, click **Lihat** button
- Opens PDF in new tab
- Can save/print from browser

### Deleting a Payment
1. Find payment in table
2. Click **Hapus** button
3. Confirm deletion
4. Payment removed, status recalculates automatically
5. File also deleted from storage

⚠️ **Warning**: Deletion cannot be undone

## 🔍 Common Scenarios

### Scenario 1: Full Payment Upfront
1. Create sale for Rp500.000
2. Customer pays full amount immediately
3. Go to Pembayaran Penjualan
4. Record payment: Rp500.000
5. Go back to Penjualan
6. Status changes to ✓ LUNAS (green)

### Scenario 2: Partial Payment
1. Create sale for Rp1.000.000 with deadline Dec 31
2. Customer pays Rp600.000 on Dec 1
3. Record payment: Rp600.000
4. Status shows ⚠ KURANG BAYAR (yellow)
5. Remaining: Rp400.000 due

### Scenario 3: Late Payment
1. Sale: Rp1.000.000 with deadline Dec 31
2. No payment by deadline
3. Customer pays Rp500.000 on Jan 15
4. Record payment: Rp500.000
5. Status shows ⛔ TELAT BAYAR (red)
6. Still owed: Rp500.000

### Scenario 4: Installment Plan
1. Sale: Rp3.000.000 with deadline 3 months
2. Customer pays:
   - Payment 1: Rp1.000.000 (status: KURANG BAYAR)
   - Payment 2: Rp1.000.000 (status: KURANG BAYAR)
   - Payment 3: Rp1.000.000 (status: LUNAS ✓)

## 📋 Data Entry Tips

### Formatting
- **Amounts**: Just numbers (no Rp, no dots/commas)
  - Wrong: `Rp500.000`
  - Right: `500000`
  
- **Dates**: Use calendar picker (YYYY-MM-DD format)
  
- **Files**: PDF only, max 5MB
  - Good: Receipt.pdf (300KB)
  - Bad: Receipt.jpg, Invoice.docx, Video.pdf (10MB)

### Best Practices
1. Always upload proof for large payments
2. Use meaningful file names (helps with searching)
3. Set realistic payment deadlines
4. Record payments promptly (same day if possible)
5. Delete payments only to correct errors
6. Check status badges regularly

## ⚙️ Technical Details

### Database Tables
- `penjualans`: Sales records with deadline and status
- `pembayaran_penjualans`: Individual payment records

### File Storage
- Location: `/storage/app/public/bukti_bayar/`
- Naming: `{timestamp}_{customer_name}.pdf`
- Accessible via: Public link in table

### API Endpoints
- `GET /transaksi/pembayaran-penjualan` - View page
- `POST /pembayaran-penjualan` - Create payment
- `DELETE /pembayaran-penjualan/{id}` - Delete payment

## ❓ FAQ

**Q: Can I edit a payment?**
A: No. Delete it and create a new one with correct amount.

**Q: What if payment deadline is in the past?**
A: It still works. Just shows when status becomes "TELAT BAYAR".

**Q: Can customer pay more than owed?**
A: Yes, system will mark as LUNAS when amount exceeds total.

**Q: What happens to status if I delete a payment?**
A: System recalculates automatically based on remaining payments.

**Q: Can I upload non-PDF files?**
A: No, only PDF files allowed. Max 5MB per file.

**Q: How long are files kept?**
A: Until payment record is deleted.

**Q: Can I see payment history for a customer?**
A: Currently view via Pembayaran Penjualan table. Customer detail view coming soon.

## 📞 Support

For issues or questions:
1. Check this guide first
2. Review PAYMENT_SYSTEM_IMPLEMENTATION.md for technical details
3. Check database migrations in `/database/migrations/`

---

**Last Updated**: 2025-11-24
**Version**: 1.0
**Status**: Production Ready ✅
