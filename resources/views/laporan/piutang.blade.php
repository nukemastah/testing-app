<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Piutang - Dashboard Admin</title>
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

        .content-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
        }

        .filter-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
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
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>📋 Laporan Piutang</h1>
        </div>
        
        <div class="filter-section">
            <form method="GET" class="filter-form">
                <label for="start_date">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
                
                <label for="end_date">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
                
                <button type="submit" class="filter-btn">Filter</button>
            </form>
        </div>

        <div class="summary-grid" style="margin-bottom:20px; display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px;">
            <div class="summary-box">
                <label>Total Piutang</label>
                <div class="value">Rp{{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <label>Total Terbayar</label>
                <div class="value">Rp{{ number_format($totalPaid ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-box">
                <label>Total Sisa</label>
                <div class="value">Rp{{ number_format($totalOutstanding ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">Daftar Piutang</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>No. Nota</th>
                        <th>Status</th>
                        <th>Total Piutang</th>
                        <th>Terbayar</th>
                        <th>Sisa Piutang</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($piutangList) && count($piutangList) > 0)
                        @foreach($piutangList as $p)
                            <tr>
                                <td>{{ $p['tanggal'] instanceof \Carbon\Carbon ? $p['tanggal']->format('d M Y') : $p['tanggal'] }}</td>
                                <td>{{ $p['pelanggan'] }}</td>
                                <td>{{ $p['no_nota'] }}</td>
                                <td>
                                    @if($p['status'] == 'lunas')
                                        <span style="color: #28a745; font-weight: bold;">Lunas</span>
                                    @elseif($p['status'] == 'sebagian')
                                        <span style="color: #ffc107; font-weight: bold;">Sebagian</span>
                                    @else
                                        <span style="color: #dc3545; font-weight: bold;">Belum Bayar</span>
                                    @endif
                                </td>
                                <td>Rp{{ number_format($p['total_harga'], 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($p['total_bayar'], 0, ',', '.') }}</td>
                                <td><strong>Rp{{ number_format($p['outstanding'], 0, ',', '.') }}</strong></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" style="text-align:center; font-weight:700">TOTAL</td>
                            <td>Rp{{ number_format($totalPiutang ?? 0, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($totalPaid ?? 0, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($totalOutstanding ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="7" class="no-data">Belum ada data piutang</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
