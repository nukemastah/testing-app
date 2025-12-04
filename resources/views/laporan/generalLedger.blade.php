<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan General Ledger</title>
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

        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: 1px solid #e0d5cc;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 150px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            padding: 10px 25px;
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
            font-size: 13px;
        }

        table thead {
            background: #f8f6f3;
            border-bottom: 2px solid #b4746f;
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
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        table tbody tr:hover {
            background: #f8f6f3;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .type-sales {
            background: #d4edda;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #155724;
        }

        .type-purchase {
            background: #f8d7da;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #721c24;
        }

        .type-payment {
            background: #d1ecf1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #0c5460;
        }

        .total-row {
            background: #f0ebe6;
            font-weight: bold;
            border-top: 2px solid #b4746f;
            border-bottom: 2px solid #b4746f;
        }

        .total-row td {
            padding: 15px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>📋 Laporan General Ledger</h1>
        </div>

        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label for="start_date">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate->toDateString() }}">
                </div>
                <div class="form-group">
                    <label for="end_date">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate->toDateString() }}">
                </div>
                <button type="submit" class="btn">Lihat Laporan</button>
            </form>
        </div>

        <div class="table-card">
            <h3>General Ledger - Periode {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}</h3>
            
            @if (count($entries) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Akun</th>
                            <th>Keterangan</th>
                            <th class="text-right">Debit (Rp)</th>
                            <th class="text-right">Kredit (Rp)</th>
                            <th class="text-center">Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($entry['date'])->format('d M Y') }}</td>
                                <td><strong>{{ $entry['account'] }}</strong></td>
                                <td>{{ $entry['description'] }}</td>
                                <td class="text-right">
                                    @if ($entry['debit'] > 0)
                                        {{ number_format($entry['debit'], 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if ($entry['credit'] > 0)
                                        {{ number_format($entry['credit'], 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($entry['type'] === 'sales')
                                        <span class="type-sales">Penjualan</span>
                                    @elseif ($entry['type'] === 'purchase')
                                        <span class="type-purchase">Pembelian</span>
                                    @else
                                        <span class="type-payment">Pembayaran</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" class="text-right"><strong>TOTAL</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalCredit, 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    <p>Tidak ada data untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
