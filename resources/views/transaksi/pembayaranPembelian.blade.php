<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pembelian - Dashboard Admin</title>
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
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .form-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
            margin-bottom: 30px;
        }

        .form-card h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #b4746f;
            box-shadow: 0 0 0 3px rgba(180, 116, 111, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180, 116, 111, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        .btn-danger {
            background: #dc3545;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-danger:hover {
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
            overflow-x: auto;
        }

        .table-card h3 {
            margin-bottom: 20px;
            color: #333;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table thead {
            background: #f8f6f3;
            border-bottom: 2px solid #ddd;
        }

        table th {
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        table tbody tr:hover {
            background: #f8f6f3;
        }

        .text-right {
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .form-button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>💰 Pembayaran Pembelian</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Tambah Pembayaran -->
        <div class="form-card">
            <h3>Catat Pembayaran Pembelian Baru</h3>
            <form action="{{ route('pembayaran-pembelian.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pembelian_id">Pembelian Barang</label>
                        <select name="pembelian_id" id="pembelian_id" required onchange="loadDetailPembelian()">
                            <option value="">-- Pilih Pembelian --</option>
                            @foreach ($pembelians as $pembelian)
                                <option value="{{ $pembelian->id }}">
                                    {{ $pembelian->barang->nama }} - {{ $pembelian->jumlah }} unit (Rp{{ number_format($pembelian->total_harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_bayar">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" id="jumlah_bayar" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_pembayaran">Tanggal Pembayaran</label>
                        <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" value="{{ old('tanggal_pembayaran', \Carbon\Carbon::now()->toDateString()) }}">
                    </div>
                </div>

                <!-- Info Box -->
                <div class="form-row">
                    <div class="form-group" style="background: #f9f7f4; padding: 15px; border-radius: 6px;">
                        <label style="margin-bottom: 0; color: #666; font-size: 12px;">Total Stok</label>
                        <div id="totalStok" style="font-size: 18px; font-weight: bold; color: #333;">-</div>
                    </div>

                    <div class="form-group" style="background: #f9f7f4; padding: 15px; border-radius: 6px;">
                        <label style="margin-bottom: 0; color: #666; font-size: 12px;">Total Harus Dibayar</label>
                        <div id="totalHarus" style="font-size: 18px; font-weight: bold; color: #333;">-</div>
                    </div>

                    <div class="form-group" style="background: #f9f7f4; padding: 15px; border-radius: 6px;">
                        <label style="margin-bottom: 0; color: #666; font-size: 12px;">Sisa Belum Dibayar</label>
                        <div id="sisaBayar" style="font-size: 18px; font-weight: bold; color: #dc3545;">-</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="bukti_bayar">Bukti Pembayaran (PDF, Max 5MB)</label>
                    <input type="file" name="bukti_bayar" id="bukti_bayar" accept=".pdf">
                </div>

                <div class="form-button-group">
                    <button type="submit" class="btn">Simpan Pembayaran</button>
                    <button type="reset" class="btn btn-secondary">Bersihkan Form</button>
                </div>
            </form>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="table-card">
            <h3>Daftar Pembayaran Pembelian</h3>
            
            @if ($pembayarans->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Jumlah Bayar</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Bukti Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayarans as $key => $pembayaran)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><strong>{{ $pembayaran->pembelian->barang->nama ?? '-' }}</strong></td>
                                <td class="text-right"><strong>Rp{{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d M Y') }}</td>
                                <td>
                                    @if ($pembayaran->bukti_bayar)
                                        <a href="{{ Storage::url($pembayaran->bukti_bayar) }}" target="_blank" class="btn" style="padding: 6px 12px; font-size: 12px;">Lihat</a>
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('pembayaran-pembelian.destroy', $pembayaran) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus pembayaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <p>Belum ada pembayaran. Catat pembayaran pertama Anda di atas.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function loadDetailPembelian() {
            const pembelianId = document.getElementById('pembelian_id').value;
            
            if (!pembelianId) {
                document.getElementById('totalStok').textContent = '-';
                document.getElementById('totalHarus').textContent = '-';
                document.getElementById('sisaBayar').textContent = '-';
                return;
            }

            fetch(`/pembayaran-pembelian/${pembelianId}/detail`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalStok').textContent = data.jumlah + ' unit';
                    document.getElementById('totalHarus').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(data.total_harga);
                    document.getElementById('sisaBayar').textContent = 'Rp' + new Intl.NumberFormat('id-ID').format(data.outstanding);
                    
                    // Set max jumlah_bayar to outstanding amount
                    document.getElementById('jumlah_bayar').max = data.outstanding;
                    document.getElementById('jumlah_bayar').placeholder = 'Max: Rp' + new Intl.NumberFormat('id-ID').format(data.outstanding);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat detail pembelian');
                });
        }
    </script>
</body>
</html>
