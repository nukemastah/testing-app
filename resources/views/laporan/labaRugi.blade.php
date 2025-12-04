<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
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

        .report-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #b4746f;
        }

        .report-title h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .report-title p {
            color: #666;
            font-size: 14px;
        }

        .report-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            font-size: 15px;
        }

        .report-row.header {
            font-weight: bold;
            color: #333;
            background: #f8f6f3;
        }

        .report-row.total {
            font-weight: bold;
            color: #333;
            background: #f0ebe6;
            border-top: 2px solid #b4746f;
            border-bottom: 2px solid #b4746f;
        }

        .report-row.subtotal {
            font-weight: 600;
            color: #555;
            background: #faf9f7;
        }

        .label {
            flex: 1;
            color: #555;
        }

        .amount {
            text-align: right;
            min-width: 150px;
            color: #333;
        }

        .amount-negative {
            color: #dc3545;
        }

        .amount-positive {
            color: #28a745;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .summary-box {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .summary-box.income {
            background: #d4edda;
            border-left: 4px solid #28a745;
        }

        .summary-box.expense {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .summary-box.profit {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            grid-column: 1 / -1;
        }

        .summary-box label {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .summary-box .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .summary-box.profit .value {
            font-size: 32px;
        }

        .margin-info {
            margin-top: 10px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>📊 Laporan Laba Rugi</h1>
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

        <div class="report-card">
            <div class="report-title">
                <h2>LAPORAN LABA RUGI</h2>
                <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
            </div>

            <div class="report-row header">
                <span class="label">Keterangan</span>
                <span class="amount">Jumlah (Rp)</span>
            </div>

            <div class="report-row">
                <span class="label">Pendapatan Penjualan</span>
                <span class="amount amount-positive">{{ number_format($revenue, 0, ',', '.') }}</span>
            </div>

            <div class="report-row">
                <span class="label">Biaya Pembelian Barang</span>
                <span class="amount amount-negative">({{ number_format($cogs, 0, ',', '.') }})</span>
            </div>

            <div class="report-row subtotal">
                <span class="label">Laba Kotor</span>
                <span class="amount">{{ number_format($grossProfit, 0, ',', '.') }}</span>
            </div>

            <div class="report-row">
                <span class="label">Biaya Operasional</span>
                <span class="amount">-</span>
            </div>

            <div class="report-row total">
                <span class="label">Laba Bersih</span>
                <span class="amount">{{ number_format($netProfit, 0, ',', '.') }}</span>
            </div>

            <div class="summary-grid">
                <div class="summary-box income">
                    <label>Pendapatan Total</label>
                    <div class="value">Rp{{ number_format($revenue, 0, ',', '.') }}</div>
                </div>

                <div class="summary-box expense">
                    <label>Biaya Total</label>
                    <div class="value">Rp{{ number_format($cogs, 0, ',', '.') }}</div>
                </div>

                <div class="summary-box profit">
                    <label>Laba Bersih</label>
                    <div class="value">Rp{{ number_format($netProfit, 0, ',', '.') }}</div>
                    <div class="margin-info">Margin Keuntungan: {{ $profitMargin }}%</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
