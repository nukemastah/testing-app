<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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

        .sidebar {
            width: 200px;
            background: linear-gradient(to bottom, #b4746f, #8b5a57);
            padding: 20px 0;
            position: fixed;
            height: 100vh;
        }

        .sidebar-menu {
            list-style: none;
            margin-top: 50px;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: block;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: #d4a574;
            color: #333;
        }

        .main-content {
            margin-left: 200px;
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

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-form label {
            font-weight: bold;
            color: #333;
        }

        .filter-form input[type="date"] {
            padding: 8px 12px;
            border: 2px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-btn {
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            min-height: 200px;
        }

        .table-header {
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            padding: 10px 15px;
            font-size: 1rem;
            font-weight: bold;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-height: 80px;
            font-size: 13px;
        }

        .data-table th {
            background: #e8e2dd;
            padding: 1px 10px;
            text-align: center;
            font-weight: bold;
            color: #333;
            border: 1px solid #d0c7bf;
            font-size: 12px;
        }

        .data-table td {
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #d0c7bf;
            color: #333;
            vertical-align: middle;
            font-size: 12px;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9f7f4;
        }

        .data-table tbody tr:hover {
            background-color:#f9f7f4;;
        }

        .no-data {
            padding: 30px 20px;
            text-align: center;
            color: #666;
            font-style: italic;
            font-size: 14px;
        }

        .logout-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        .logout-btn:hover {
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
            
            .filter-form {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .logout-btn {
                left: 10px;
                bottom: 10px;
                padding: 10px 20px;
                font-size: 12px;
            }

            .data-table {
                font-size: 11px;
            }

            .data-table th, .data-table td {
                padding: 6px 8px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('barang.index') }}">Kelola Barang</a></li>
            <li><a href="{{ route('penjualan.index') }}">Penjualan</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>DASHBOARD ADMIN</h1>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                <label for="tanggal">Tanggal Penjualan:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                
                <label for="tanggal_barang">Tanggal Barang:</label>
                <input type="date" id="tanggal_barang" name="tanggal_barang" value="{{ request('tanggal_barang') }}">
                
                <button type="submit" class="filter-btn">Filter</button>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                Data Penjualan
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                    <th>TANGGAL PENJUALAN</th>    
                    <th>NAMA BARANG</th>
                    <th>JUMLAH BARANG</th>
                    <th>TOTAL HARGA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penjualans as $p)
                    <tr>
                        <td>{{ $p->tanggal->format('l, d/m/Y') }}</td>    
                        <td>{{ $p->barang->nama ?? '-' }}</td>
                        <td>{{ $p->jumlah }} pcs</td>
                        <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="no-data">Tidak ada data penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $penjualans->links('pagination::tailwind') }}
                </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                Data Barang
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA BARANG</th>
                        <th>HARGA</th>
                        <th>STOK</th>
                        <th>TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $i => $b)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $b->nama }}</td>
                        <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                        <td>{{ $b->kuantitas }}</td>
                        <td>{{ $b->created_at->format('l, d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="no-data">Tidak ada data barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4 flex justify-between items-center">
    @if ($barangs->onFirstPage())
        <span class="text-gray-400">← Prev</span>
    @else
        <a href="{{ $barangs->previousPageUrl() }}" class="text-blue-500 hover:underline">← Prev</a>
    @endif

    @if ($barangs->hasMorePages())
        <a href="{{ $barangs->nextPageUrl() }}" class="text-blue-500 hover:underline">Next →</a>
    @else
        <span class="text-gray-400">Next →</span>
    @endif
</div>
            <div class="table-container">
            <div class="table-header">
                Laporan Laba Rugi
            </div>
            <table class="data-table">
                <thead>
            <tr>
                <th >Tanggal</th>
                <th >Nama Barang</th>
                <th >Harga Beli</th>
                <th >Harga Jual</th>
                <th >Laba</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $row)
                <tr>
                    <td >{{ $row['tanggal'] }}</td>
                    <td >{{ $row['nama_barang'] }}</td>
                    <td >Rp {{ number_format($row['harga_beli']) }}</td>
                    <td >Rp {{ number_format($row['harga_jual']) }}</td>
                    <td >Rp {{ number_format($row['laba']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center p-2">Belum ada data penjualan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    </div>
    
    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
        @csrf
        <button type="submit" class="logout-btn">LOGOUT</button>
    </form>
</body>
</html>