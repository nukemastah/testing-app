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
            position: relative;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #333;
            font-weight: bold;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .header-logout {
            position: absolute;
            top: 0;
            right: 0;
        }

        .filter-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .filter-form {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-form label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .filter-form input[type="date"] {
            padding: 10px 15px;
            border: 2px solid #d0c7bf;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            transition: all 0.3s;
        }

        .filter-form input[type="date"]:focus {
            outline: none;
            border-color: #b4746f;
            box-shadow: 0 0 0 3px rgba(180, 116, 111, 0.1);
        }

        .filter-btn {
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180, 116, 111, 0.3);
        }

        .print-all-btn {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.3);
        }

        .print-all-btn::before {
            content: '🖨️';
            font-size: 16px;
        }

        .table-container {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            border: 1px solid #e8ddd4;
        }

        .table-header {
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            padding: 15px 20px;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .print-btn {
            background: linear-gradient(135deg, #28a745, #20903c);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            background: white;
        }

        .data-table th {
            background: linear-gradient(135deg, #f0ebe6 0%, #e8ddd4 100%);
            padding: 15px 12px;
            text-align: center;
            font-weight: 600;
            color: #444;
            border-bottom: 2px solid #d0c7bf;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table td {
            padding: 15px 12px;
            text-align: center;
            border-bottom: 1px solid #f0ebe6;
            color: #555;
            vertical-align: middle;
            font-size: 13px;
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

        .no-data {
            padding: 40px 20px;
            text-align: center;
            color: #888;
            font-style: italic;
            font-size: 16px;
        }

        /* Pagination Styles */
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

        .pagination-btn:hover {
            background: linear-gradient(135deg, #8b5a57, #6d453f);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(180, 116, 111, 0.3);
        }

        .pagination-btn.disabled {
            background: linear-gradient(135deg, #ccc, #bbb);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .pagination-btn.disabled:hover {
            background: linear-gradient(135deg, #ccc, #bbb);
            transform: none;
            box-shadow: none;
        }

        .pagination-info {
            margin: 0 20px;
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .logout-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }

        /* Arrow icons for pagination */
        .pagination-btn::before {
            content: '';
            margin-right: 8px;
        }

        .pagination-btn.prev::before {
            content: '←';
            margin-right: 5px;
        }

        .pagination-btn.next::after {
            content: '→';
            margin-left: 5px;
        }

        /* Print styles */
        @media print {
            .sidebar, .header-logout, .pagination-container, .print-btn, .filter-section, .print-all-btn {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .table-container {
                break-inside: avoid;
                margin-bottom: 20px;
                box-shadow: none;
                border: 1px solid #ccc;
                page-break-inside: avoid;
            }
            
            .data-table {
                font-size: 10px;
            }
            
            .data-table th, .data-table td {
                padding: 5px;
                font-size: 10px;
            }
            
            body {
                background: white;
            }
            
            .table-header {
                background: #666 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
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
            
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-form {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .print-all-btn {
                align-self: center;
                margin-top: 10px;
            }
            
            .header-logout {
                position: static;
                text-align: center;
                margin-top: 15px;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th, .data-table td {
                padding: 10px 8px;
                font-size: 12px;
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

            .table-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-content">
        <div class="header">
            <h1>DASHBOARD ADMIN</h1>
            <div class="header-logout">
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">LOGOUT</button>
                </form>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
                <label for="tanggal">Tanggal Penjualan:</label>
                <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                
                <label for="tanggal_barang">Tanggal Barang:</label>
                <input type="date" id="tanggal_barang" name="tanggal_barang" value="{{ request('tanggal_barang') }}">
                
                <button type="submit" class="filter-btn">Filter</button>
            </form>
            
            <button onclick="printAllPDF()" class="print-all-btn">Print All PDF</button>
        </div>

        <div class="table-container">
            <div class="table-header">
                <span>Data Penjualan</span>
                <button onclick="printSingleTable('penjualan')" class="print-btn">Print PDF</button>
            </div>
            <table class="data-table" id="table-penjualan">
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
                        <td colspan="4" class="no-data">Tidak ada data penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-container">
                <div class="pagination">
                    @if ($penjualans->onFirstPage())
                        <button class="pagination-btn prev disabled" disabled>Previous</button>
                    @else
                        <a href="{{ $penjualans->previousPageUrl() }}" class="pagination-btn prev">Previous</a>
                    @endif

                    <div class="pagination-info">
                        Page {{ $penjualans->currentPage() }} of {{ $penjualans->lastPage() }}
                    </div>

                    @if ($penjualans->hasMorePages())
                        <a href="{{ $penjualans->nextPageUrl() }}" class="pagination-btn next">Next</a>
                    @else
                        <button class="pagination-btn next disabled" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <span>Data Barang</span>
                <button onclick="printSingleTable('barang')" class="print-btn">Print PDF</button>
            </div>
            <table class="data-table" id="table-barang">
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
            <div class="pagination-container">
                <div class="pagination">
                    @if ($barangs->onFirstPage())
                        <button class="pagination-btn prev disabled" disabled>Previous</button>
                    @else
                        <a href="{{ $barangs->previousPageUrl() }}" class="pagination-btn prev">Previous</a>
                    @endif

                    <div class="pagination-info">
                        Page {{ $barangs->currentPage() }} of {{ $barangs->lastPage() }}
                    </div>

                    @if ($barangs->hasMorePages())
                        <a href="{{ $barangs->nextPageUrl() }}" class="pagination-btn next">Next</a>
                    @else
                        <button class="pagination-btn next disabled" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <span>Laporan Laba Rugi</span>
                <button onclick="printSingleTable('laporan')" class="print-btn">Print PDF</button>
            </div>
            <table class="data-table" id="table-laporan">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Barang</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Laba</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $row)
                        <tr>
                            <td>{{ $row['tanggal'] }}</td>
                            <td>{{ $row['nama_barang'] }}</td>
                            <td>Rp {{ number_format($row['harga_beli']) }}</td>
                            <td>Rp {{ number_format($row['harga_jual']) }}</td>
                            <td>Rp {{ number_format($row['laba']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">Belum ada data penjualan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-container">
                <div class="pagination">
                    @if ($laporan->onFirstPage())
                        <button class="pagination-btn prev disabled" disabled>Previous</button>
                    @else
                        <a href="{{ $laporan->previousPageUrl() }}" class="pagination-btn prev">Previous</a>
                    @endif

                    <div class="pagination-info">
                        Page {{ $laporan->currentPage() }} of {{ $laporan->lastPage() }}
                    </div>

                    @if ($laporan->hasMorePages())
                        <a href="{{ $laporan->nextPageUrl() }}" class="pagination-btn next">Next</a>
                    @else
                        <button class="pagination-btn next disabled" disabled>Next</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk print semua tabel
        function printAllPDF() {
            window.print();
        }

        // Fungsi untuk print tabel individual
        function printSingleTable(tableType) {
            // Sembunyikan semua tabel kecuali yang dipilih
            const allTables = document.querySelectorAll('.table-container');
            const targetTable = document.getElementById('table-' + tableType).closest('.table-container');
            
            // Simpan display asli
            const originalDisplays = [];
            allTables.forEach((table, index) => {
                originalDisplays[index] = table.style.display;
                if (table !== targetTable) {
                    table.style.display = 'none';
                }
            });
            
            // Print
            window.print();
            
            // Kembalikan display asli
            allTables.forEach((table, index) => {
                table.style.display = originalDisplays[index];
            });
        }
    </script>
</body>
</html>