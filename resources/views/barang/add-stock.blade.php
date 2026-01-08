<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Stok - {{ $barang->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f1eb 0%, #e8ddd4 100%);
            display: flex;
            min-height: 100vh;
        }

        .main-container {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
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

        .card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
            max-width: 600px;
            margin: 0 auto;
        }

        .barang-info {
            background: linear-gradient(135deg, #f0ebe6 0%, #e8ddd4 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #b4746f;
        }

        .barang-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #333;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #d0c7bf;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #b4746f;
            box-shadow: 0 0 0 3px rgba(180, 116, 111, 0.1);
        }

        .summary-box {
            background: linear-gradient(135deg, #f9f7f4 0%, #f0ebe6 100%);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #e0d5cc;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            font-size: 14px;
        }

        .summary-row .label {
            color: #666;
            font-weight: 600;
        }

        .summary-row .value {
            color: #333;
            font-weight: bold;
        }

        .summary-row.total {
            border-top: 2px solid #d0c7bf;
            padding-top: 12px;
            margin-top: 12px;
            font-size: 16px;
        }

        .summary-row.total .value {
            color: #b4746f;
            font-size: 18px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180, 116, 111, 0.3);
        }

        .btn-secondary {
            background: #d0c7bf;
            color: #333;
        }

        .btn-secondary:hover {
            background: #c0b7af;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 15px;
            }

            .card {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-container">
        <div class="header">
            <h1>➕ Tambah Stok Barang</h1>
        </div>

        <div class="card">
            <!-- Display current barang info -->
            <div class="barang-info">
                <h3>Informasi Barang Saat Ini</h3>
                <div class="info-row">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $barang->nama }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Stok Saat Ini:</span>
                    <span class="info-value">{{ $barang->kuantitas }} unit</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Harga Beli (Master):</span>
                    <span class="info-value">Rp{{ number_format($barang->harga, 0, ',', '.') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Harga Jual (Master):</span>
                    <span class="info-value">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Terjadi Kesalahan!</strong>
                    <ul style="margin-top: 10px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('barang.addStock', $barang->id) }}">
                @csrf

                <div class="form-group">
                    <label for="jumlah">Jumlah Stok Ditambahkan *</label>
                    <input type="number" id="jumlah" name="jumlah" min="1" required value="{{ old('jumlah') }}" onchange="calculateTotal()">
                    @error('jumlah')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="harga_beli">Harga Beli per Unit *</label>
                    <input type="number" id="harga_beli" name="harga_beli" min="0" required value="{{ old('harga_beli', $barang->harga) }}" onchange="calculateTotal()">
                    @error('harga_beli')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pemasok_id">Pemasok *</label>
                    <select id="pemasok_id" name="pemasok_id" required>
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($pemasoks as $pemasok)
                            <option value="{{ $pemasok->id }}" {{ old('pemasok_id') == $pemasok->id ? 'selected' : '' }}>
                                {{ $pemasok->nama_pemasok }}
                            </option>
                        @endforeach
                    </select>
                    @error('pemasok_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal_pembelian">Tanggal Pembelian *</label>
                    <input type="date" id="tanggal_pembelian" name="tanggal_pembelian" required value="{{ old('tanggal_pembelian', date('Y-m-d')) }}">
                    @error('tanggal_pembelian')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal_kadaluarsa">Tanggal Kadaluarsa (Opsional)</label>
                    <input type="date" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}" min="{{ date('Y-m-d') }}">
                    <small style="color: #666; font-size: 12px;">Kosongkan jika barang tidak memiliki tanggal kadaluarsa</small>
                    @error('tanggal_kadaluarsa')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Summary Section -->
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="label">Jumlah:</span>
                        <span class="value" id="summaryJumlah">0 unit</span>
                    </div>
                    <div class="summary-row">
                        <span class="label">Harga per Unit:</span>
                        <span class="value" id="summaryHarga">Rp 0</span>
                    </div>
                    <div class="summary-row total">
                        <span class="label">Total Harga:</span>
                        <span class="value" id="summaryTotal">Rp 0</span>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #d0c7bf; font-size: 12px; color: #666;">
                        <strong>Catatan:</strong> Stok barang akan langsung bertambah, dan data pembelian akan tercatat di sistem pembelian dengan status "Belum Bayar". Pembayaran dapat dilakukan melalui menu Pembayaran Pembelian.
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Tambah Stok</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            const hargaBeli = parseInt(document.getElementById('harga_beli').value) || 0;
            const total = jumlah * hargaBeli;

            document.getElementById('summaryJumlah').textContent = jumlah + ' unit';
            document.getElementById('summaryHarga').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(hargaBeli);
            document.getElementById('summaryTotal').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(total);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</body>
</html>
