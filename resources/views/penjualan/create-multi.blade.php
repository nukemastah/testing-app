<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penjualan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f1eb;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #333;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table thead {
            background: #e8e2dd;
            border: 1px solid #d0c7bf;
        }

        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }

        .items-table td {
            padding: 12px;
            border: 1px solid #d0c7bf;
            background: white;
        }

        .items-table select,
        .items-table input,
        .items-table span {
            font-size: 16px;
        }

        .items-table tbody tr:hover {
            background-color: #f9f7f4;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-add {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .summary {
            background: #f9f7f4;
            padding: 20px;
            border-radius: 6px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }

        .summary-row.total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        .input-qty {
            width: 100px;
        }

        .readonly {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        .error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .hidden-row {
            display: none;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .items-table {
                font-size: 12px;
            }

            .items-table th,
            .items-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-content">
        <div class="header">
            <h1>FORM PENJUALAN</h1>
        </div>

        <div class="form-container">
            <form id="penjualanForm" method="POST" action="{{ route('penjualan.store') }}">
                @csrf

                <!-- Customer Selection -->
                <div class="grid-2">
                    <div class="form-group">
                        <label for="pelanggan_id">Pelanggan *</label>
                        <select name="pelanggan_id" id="pelanggan_id" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}" {{ old('pelanggan_id') == $pelanggan->id ? 'selected' : '' }}>
                                    {{ $pelanggan->kode_pelanggan ?? ('P-' . $pelanggan->id) }} - {{ $pelanggan->nama_pelanggan }}
                                </option>
                            @endforeach
                        </select>
                        @error('pelanggan_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tenggat_pembayaran">Tenggat Pembayaran</label>
                        <input type="date" name="tenggat_pembayaran" id="tenggat_pembayaran" 
                               value="{{ old('tenggat_pembayaran', \Carbon\Carbon::now()->addDays(30)->toDateString()) }}">
                        @error('tenggat_pembayaran')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Items Table -->
                <h3 style="margin-top: 30px; margin-bottom: 15px; color: #333;">Daftar Barang yang Dijual</h3>
                <table class="items-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th width="35%">Barang</th>
                            <th width="15%">Harga Jual</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Items will be added here -->
                    </tbody>
                </table>

                <!-- Summary -->
                <div class="summary">
                    <div class="summary-row">
                        <span>Total Item:</span>
                        <span id="totalItems">0</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Harga:</span>
                        <span id="totalHarga">Rp 0</span>
                    </div>
                    <div class="summary-row total">
                        <span>GRAND TOTAL:</span>
                        <span id="grandTotal">Rp 0</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-group">
                    <button type="button" class="btn btn-add" onclick="addItem()">+ Tambah Item</button>
                    <button type="submit" class="btn btn-submit" id="submitBtn">Proses Penjualan</button>
                    <a href="{{ route('penjualan.index') }}" class="btn btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 0;
        const barangsData = {!! json_encode($barangs->mapWithKeys(fn($b) => [$b->id => ['harga' => $b->harga_jual, 'stok' => $b->kuantitas]])) !!};

        // Add first empty item row on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        function addItem() {
            const tbody = document.getElementById('itemsBody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="items[${itemCount}][barang_id]" class="barang-select" required onchange="updateHargaJual(this)">
                        @foreach($barangs as $barang)
                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}" data-stok="{{ $barang->kuantitas }}">
                                {{ $barang->nama }} (Stok: {{ $barang->kuantitas }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="items[${itemCount}][harga_jual]" class="harga-input readonly" readonly>
                </td>
                <td>
                    <input type="number" name="items[${itemCount}][quantity]" class="qty-input input-qty" min="1" required onchange="calculateSubtotal(this)">
                </td>
                <td>
                    <span class="subtotal">0</span>
                </td>
                <td>
                    <button type="button" class="btn btn-delete" onclick="removeItem(this)">Hapus</button>
                </td>
            `;
            tbody.appendChild(newRow);
            itemCount++;
        }

        function removeItem(btn) {
            const row = btn.closest('tr');
            row.remove();
            calculateTotal();
            
            // Ensure at least one row remains
            if (document.querySelectorAll('#itemsBody tr').length === 0) {
                addItem();
            }
        }

        function updateHargaJual(select) {
            const barangId = select.value;
            const row = select.closest('tr');
            const hargaInput = row.querySelector('.harga-input');
            const qtyInput = row.querySelector('.qty-input');
            
            if (barangId && barangsData[barangId]) {
                const harga = barangsData[barangId].harga;
                const stok = barangsData[barangId].stok;
                hargaInput.value = harga;
                qtyInput.max = stok;
                qtyInput.value = 1;
                calculateSubtotal(qtyInput);
            } else {
                hargaInput.value = '';
                qtyInput.max = '';
                qtyInput.value = '';
                calculateSubtotal(qtyInput);
            }
        }

        function calculateSubtotal(qtyInput) {
            const row = qtyInput.closest('tr');
            const hargaInput = row.querySelector('.harga-input');
            const subtotalSpan = row.querySelector('.subtotal');
            
            const quantity = parseInt(qtyInput.value) || 0;
            const harga = parseInt(hargaInput.value) || 0;
            const subtotal = quantity * harga;
            
            subtotalSpan.textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(subtotal);
            
            calculateTotal();
        }

        function calculateTotal() {
            let totalItems = 0;
            let totalHarga = 0;

            document.querySelectorAll('#itemsBody tr').forEach(row => {
                const qtyInput = row.querySelector('.qty-input');
                const hargaInput = row.querySelector('.harga-input');
                
                const quantity = parseInt(qtyInput.value) || 0;
                const harga = parseInt(hargaInput.value) || 0;
                
                if (qtyInput.value && hargaInput.value) {
                    totalItems += quantity;
                    totalHarga += quantity * harga;
                }
            });

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalHarga').textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(totalHarga);
            document.getElementById('grandTotal').textContent = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(totalHarga);
        }

        // Validate before submit
        document.getElementById('penjualanForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#itemsBody tr');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Minimal ada 1 item yang harus dijual!');
                return;
            }

            let validItems = 0;
            rows.forEach(row => {
                const barangSelect = row.querySelector('.barang-select');
                const qtyInput = row.querySelector('.qty-input');
                if (barangSelect.value && qtyInput.value) {
                    validItems++;
                }
            });

            if (validItems === 0) {
                e.preventDefault();
                alert('Minimal ada 1 item yang harus dikonfigurasi dengan baik!');
                return;
            }
        });
    </script>
</body>
</html>
