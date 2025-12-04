<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Dashboard Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .charts-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .chart-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
        }

        .chart-card h3 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
        }

        .table-container {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
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

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f6f3 100%);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #e0d5cc;
            text-align: center;
        }

        .summary-card h4 {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .summary-card .value {
            color: #b4746f;
            font-size: 1.8rem;
            font-weight: bold;
        }

        @media (max-width: 1200px) {
            .charts-container {
                grid-template-columns: 1fr;
            }

            .summary-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>📉 Laporan Penjualan</h1>
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

        <div class="summary-cards">
            <div class="summary-card">
                <h4>Total Penjualan</h4>
                <div class="value">Rp{{ number_format($total ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h4>Jumlah Transaksi</h4>
                <div class="value">{{ $count ?? 0 }}</div>
            </div>
            <div class="summary-card">
                <h4>Rata-rata Penjualan</h4>
                <div class="value">Rp{{ number_format($avg ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card">
                <h4>Barang Terjual</h4>
                <div class="value">{{ $itemsSold ?? 0 }}</div>
            </div>
        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Grafik Penjualan per Hari</h3>
                <div class="chart-wrapper">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3>Top 5 Barang Penjualan</h3>
                <div class="chart-wrapper">
                    <canvas id="topItemsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">Daftar Penjualan Detail</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Nomor Invoice</th>
                        <th>Pelanggan</th>
                        <th>Total Barang</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($penjualanList) && count($penjualanList) > 0)
                        @foreach($penjualanList as $p)
                            <tr>
                                <td>{{ $p->tanggal instanceof \Carbon\Carbon ? $p->tanggal->format('d M Y') : $p->tanggal }}</td>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->pelanggan ? $p->pelanggan->nama_pelanggan : 'Walk-in' }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>Rp{{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="no-data">Belum ada data penjualan</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Grafik Penjualan per Hari
        const ctx1 = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels ?? []) !!},
                datasets: [{
                    label: 'Penjualan (Rp)',
                    data: {!! json_encode($data ?? []) !!},
                    borderColor: '#b4746f',
                    backgroundColor: 'rgba(180, 116, 111, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#b4746f',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#666',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Grafik Top 5 Barang
        const ctx2 = document.getElementById('topItemsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(isset($topItems) ? $topItems->pluck('nama')->toArray() : []) !!},
                datasets: [{
                    data: {!! json_encode(isset($topItems) ? $topItems->pluck('total')->toArray() : []) !!},
                    backgroundColor: [
                        '#b4746f',
                        '#d4a574',
                        '#c89078',
                        '#a0635e',
                        '#8b5a57'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#666',
                            font: {
                                size: 12
                            },
                            padding: 15
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
