<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang</title>
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
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .success-message {
            background: linear-gradient(135deg, #d1edff 0%, #bee5eb 100%);
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .undo-btn {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .undo-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 123, 255, 0.3);
        }

        .table-container {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border: 1px solid #e8ddd4;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .data-table th {
            background: linear-gradient(135deg, #f0ebe6 0%, #e8ddd4 100%);
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            color: #444;
            border-bottom: 2px solid #d0c7bf;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #f0ebe6;
            color: #555;
            font-size: 14px;
            transition: all 0.3s;
        }

        .data-table tbody tr {
            transition: all 0.3s;
        }

        .data-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #fdfcfa 0%, #f9f7f4 100%);
        }

        .data-table tbody tr:hover {
            background: linear-gradient(135deg, #f5f1eb 0%, #ede7de 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Updated Action Buttons Styles */
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        .btn-edit, .btn-hapus {
            padding: 8px 16px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, #6fa8dc, #5a8bb8);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #5a8bb8, #4a7496);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(111, 168, 220, 0.3);
        }

        .btn-hapus {
            background: linear-gradient(135deg, #e06666, #cc5555);
            color: white;
        }

        .btn-hapus:hover {
            background: linear-gradient(135deg, #cc5555, #b84444);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(224, 102, 102, 0.3);
        }

        /* Icons using CSS */
        .btn-edit::before {
            content: "✏️";
            font-size: 14px;
        }

        .btn-hapus::before {
            content: "🗑️";
            font-size: 14px;
        }

        /* Pagination Styles - FIXED */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #f8f6f3 0%, #f0ebe6 100%);
            border-top: 1px solid #e8ddd4;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            min-width: 100px;
        }

        .pagination-btn:hover:not(.disabled) {
            background: linear-gradient(135deg, #8b5a57, #6d453f);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(180, 116, 111, 0.3);
        }

        .pagination-btn.disabled {
            background: linear-gradient(135deg, #ccc, #bbb) !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
            opacity: 0.6;
            pointer-events: none;
        }

        .pagination-info {
            margin: 0 20px;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        /* Arrow icons for pagination */
        .pagination-btn.prev::before {
            content: '←';
            margin-right: 5px;
        }

        .pagination-btn.next::after {
            content: '→';
            margin-left: 5px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #333;
            font-weight: bold;
            margin: 40px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .form-section {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            border: 1px solid #e8ddd4;
            overflow: hidden;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
        }

        .form-table th {
            background: linear-gradient(135deg, #f0ebe6 0%, #e8ddd4 100%);
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            color: #444;
            border-bottom: 2px solid #d0c7bf;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-table td {
            padding: 0;
            border-bottom: 1px solid #f0ebe6;
        }

        .form-input {
            width: 100%;
            padding: 15px 12px;
            border: none;
            background: transparent;
            font-size: 14px;
            text-align: center;
            outline: none;
            transition: all 0.3s;
        }

        .form-input:focus {
            background: linear-gradient(135deg, #f5f1eb 0%, #ede7de 100%);
        }

        .btn-tambah {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 25px;
            border: none;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-tambah:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #888;
            font-style: italic;
            font-size: 16px;
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
            border-radius: 12px;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            text-decoration: none;
            z-index: 1000;
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
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

            .data-table th,
            .data-table td,
            .form-table th {
                padding: 10px 8px;
                font-size: 12px;
            }

            .form-input {
                padding: 10px 8px;
                font-size: 12px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 4px;
            }

            .btn-edit, .btn-hapus {
                padding: 6px 12px;
                font-size: 10px;
            }

            .pagination-btn {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 80px;
            }

            .pagination-info {
                font-size: 12px;
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-content">
        <div class="header">
            <h1>KELOLA BARANG</h1>
        </div>

        @if(session('success'))
        <div class="success-message">
            <span>{{ session('success') }}</span>
            @if($deletedBarang ?? false)
                <form method="POST" action="{{ route('barang.undo') }}" style="display: inline;">
                    @csrf
                    <button class="undo-btn" type="submit">Undo</button>
                </form>
            @endif
        </div>
        @endif

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>KUANTITAS</th>
                        <th>HARGA BELI</th>
                        <th>HARGA JUAL</th>
                        <th>PEMASOK</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangs as $i => $barang)
                    <tr>
                        <td>{{ sprintf('%02d', $i + 1) }}</td>
                        <td>{{ $barang->nama }}</td>
                        <td>{{ $barang->kuantitas }}</td>
                        <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                        <td>{{ $barang->pemasok->nama_pemasok ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="/barang/{{ $barang->id }}/edit" class="btn-edit">Edit</a>
                                <a href="/barang/{{ $barang->id }}/add-stock" class="btn-edit" style="background-color: #007bff;">Tambah Stok</a>
                                <form method="POST" action="/barang/{{ $barang->id }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-hapus" type="submit" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="no-data">Belum ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="pagination-container">
                <div class="pagination">
                    @if ($barangs->onFirstPage())
                        <span class="pagination-btn prev disabled">Previous</span>
                    @else
                        <a href="{{ $barangs->previousPageUrl() }}" class="pagination-btn prev">Previous</a>
                    @endif

                    <div class="pagination-info">
                        Page {{ $barangs->currentPage() }} of {{ $barangs->lastPage() }}
                    </div>

                    @if ($barangs->hasMorePages())
                        <a href="{{ $barangs->nextPageUrl() }}" class="pagination-btn next">Next</a>
                    @else
                        <span class="pagination-btn next disabled">Next</span>
                    @endif
                </div>
            </div>
        </div>

        <h2 class="section-title">TAMBAH BARANG</h2>

        <div class="form-section">
            <form method="POST" action="/barang">
                @csrf
                <table class="form-table">
                    <thead>
                        <tr>
                            <th>NAMA BARANG</th>
                            <th>HARGA BELI</th>
                            <th>HARGA JUAL</th>
                            <th>KUANTITAS</th>
                            <th>PEMASOK</th>
                            <th>TAMBAH</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="nama" class="form-input" placeholder="Masukkan nama barang" required></td>
                            <td><input type="number" name="harga" class="form-input" placeholder="0" required min="0"></td>
                            <td><input type="number" name="harga_jual" class="form-input" placeholder="0" required min="0"></td>
                            <td><input type="number" name="kuantitas" class="form-input" placeholder="0" required min="1"></td>
                            <td>
                                <select name="pemasok_id" class="form-input" style="padding: 15px 12px; border: none; background: transparent; font-size: 14px; text-align: center; outline: none; width: 100%;">
                                    <option value="">-- Pilih Pemasok --</option>
                                    @foreach($pemasoks as $pemasok)
                                    <option value="{{ $pemasok->id }}">{{ $pemasok->kode_pemasok }} - {{ $pemasok->nama_pemasok }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><button type="submit" class="btn-tambah">TAMBAH</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="back-btn">
        ←
    </a>
</body>
</html>