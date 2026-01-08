<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Inventory - {{ $barang->nama }}</title>
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

        .barang-info {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
        }

        .barang-info h3 {
            color: #b4746f;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            color: #666;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
            font-weight: bold;
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
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
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
        }

        .data-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #f0ebe6;
            color: #555;
            font-size: 13px;
        }

        .data-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #fdfcfa 0%, #f9f7f4 100%);
        }

        .data-table tbody tr:hover {
            background: linear-gradient(135deg, #f5f1eb 0%, #ede7de 100%);
            transform: scale(1.01);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #b4746f, #8b5a57);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(180, 116, 111, 0.3);
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #888;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 15px;
            }

            .header h1 {
                font-size: 1.8rem;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th, .data-table td {
                padding: 8px 6px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-container">
        <div class="header">
            <h1>📦 Batch Inventory</h1>
        </div>

        <div class="barang-info">
            <h3>Informasi Barang</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Barang</span>
                    <span class="info-value">{{ $barang->nama }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Stok (Sistem Lama)</span>
                    <span class="info-value">{{ $barang->kuantitas }} unit</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Stok (Dari Batch)</span>
                    <span class="info-value">{{ $batches->sum('stok_tersedia') }} unit</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jumlah Batch</span>
                    <span class="info-value">{{ $batches->count() }} batch</span>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                Daftar Batch
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Batch Number</th>
                        <th>Tanggal Masuk</th>
                        <th>Harga Beli</th>
                        <th>Stok Awal</th>
                        <th>Stok Tersedia</th>
                        <th>Tanggal Kadaluarsa</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td><strong>{{ $batch->batch_number }}</strong></td>
                        <td>{{ $batch->tanggal_masuk->format('d/m/Y') }}</td>
                        <td>Rp {{ number_format($batch->harga_beli, 0, ',', '.') }}</td>
                        <td>{{ $batch->stok_awal }}</td>
                        <td><strong>{{ $batch->stok_tersedia }}</strong></td>
                        <td>
                            @if($batch->tanggal_kadaluarsa)
                                {{ $batch->tanggal_kadaluarsa->format('d/m/Y') }}
                                @php
                                    $days = $batch->daysUntilExpiry();
                                @endphp
                                @if($days !== null)
                                    <br><small>({{ abs($days) }} hari {{ $days >= 0 ? 'lagi' : 'telah lewat' }})</small>
                                @endif
                            @else
                                <span class="badge badge-info">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            @if($batch->stok_tersedia == 0)
                                <span class="badge badge-danger">Habis</span>
                            @elseif($batch->isExpired())
                                <span class="badge badge-danger">Kadaluarsa</span>
                            @elseif($batch->daysUntilExpiry() !== null && $batch->daysUntilExpiry() <= 30 && $batch->daysUntilExpiry() > 0)
                                <span class="badge badge-warning">Segera Kadaluarsa</span>
                            @else
                                <span class="badge badge-success">Tersedia</span>
                            @endif
                        </td>
                        <td>{{ $batch->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="no-data">Tidak ada batch untuk barang ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expiringBatches->count() > 0)
        <div class="table-container">
            <div class="table-header" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                ⚠️ Batch yang Akan Segera Kadaluarsa (30 Hari)
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Batch Number</th>
                        <th>Stok Tersedia</th>
                        <th>Tanggal Kadaluarsa</th>
                        <th>Sisa Hari</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expiringBatches as $batch)
                    <tr>
                        <td><strong>{{ $batch->batch_number }}</strong></td>
                        <td>{{ $batch->stok_tersedia }}</td>
                        <td>{{ $batch->tanggal_kadaluarsa->format('d/m/Y') }}</td>
                        <td><strong>{{ $batch->daysUntilExpiry() }} hari</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('barang.index') }}" class="btn-back">← Kembali ke Daftar Barang</a>
        </div>
    </div>
</body>
</html>
