<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penjualan</title>
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
            margin-bottom: 30px;
        }

        .form-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form select {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            min-width: 150px;
        }

        .add-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .add-form select,
        .add-form input {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            min-width: 120px;
        }

        .add-form select {
            min-width: 200px;
        }

        .btn-jual {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-jual:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #e8e2dd;
            padding: 15px 12px;
            text-align: center;
            font-weight: bold;
            color: #333;
            border: 1px solid #d0c7bf;
            font-size: 14px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 15px 12px;
            text-align: center;
            border: 1px solid #d0c7bf;
            color: #333;
            font-size: 14px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9f7f4;
        }

        .data-table tbody tr:hover {
            background-color: #f0ebe6;
        }

        .btn-jual-table {
            background: linear-gradient(135deg,rgb(255, 3, 3),rgb(243, 55, 55));
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-cancel-table:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .no-data {
            padding: 30px;
            text-align: center;
            color: #666;
            font-style: italic;
        }

        .back-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 150px;
            }
            
            .main-content {
                margin-left: 150px;
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .add-form {
                flex-direction: column;
                align-items: stretch;
            }

            .add-form select,
            .add-form input {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-content">
        <div class="header">
            <h1>DAFTAR PENJUALAN</h1>
        </div>

        <div class="form-section">
            <!-- Filter Penjualan -->
            <form method="GET" class="filter-form">
                <select name="filter" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Semua Penjualan</option>
                    <option value="daily" {{ request('filter') == 'daily' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="weekly" {{ request('filter') == 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="monthly" {{ request('filter') == 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="yearly" {{ request('filter') == 'yearly' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </form>

            <!-- Form Input Penjualan -->
            <form method="POST" action="/penjualan" class="add-form">
                @csrf
                <select name="barang_id" required>
                    <option value="">Pilih Barang</option>
                    @foreach($barangs as $barang)
                        <option value="{{ $barang->id }}">{{ $barang->nama }} (Stok: {{ $barang->kuantitas }})</option>
                    @endforeach
                </select>
                
                <select name="pelanggan_id">
                    <option value="">Pilih Pelanggan (opsional)</option>
                    @foreach($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->id }}">{{ $pelanggan->kode_pelanggan ?? ('P-' . $pelanggan->id) }} - {{ $pelanggan->nama_pelanggan }}</option>
                    @endforeach
                </select>

                <input type="number" name="jumlah" placeholder="Jumlah" required min="1">

                <input type="number" name="harga_jual" placeholder="Harga Jual (Opsional)" min="0">

                <button type="submit" class="btn-jual">Jual</button>
            </form>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>KUANTITAS</th>
                        <th>HARGA</th>
                        <th>PELANGGAN</th>
                        <th>JUAL BARANG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $key => $p)
                    <tr>
                        <td>{{ sprintf('%02d', $key + 1) }}</td>
                        <td>{{ $p->barang ? $p->barang->nama : 'Barang tidak tersedia' }}</td>
                        <td>{{ $p->jumlah }} pcs</td>
                        <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $p->pelanggan ? $p->pelanggan->nama_pelanggan : '-' }}</td>
                        <td>
                            <form method="POST" action="{{ route('penjualan.destroy', $p->id) }}" onsubmit="return confirm('Yakin ingin membatalkan penjualan ini?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-jual-table">CANCEL</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="no-data">Belum ada data penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="back-btn">
        ←
    </a>
</body>
</html>