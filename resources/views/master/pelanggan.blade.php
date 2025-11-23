<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggan - Dashboard Admin</title>
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
            text-align: center;
        }

        .content-card p {
            color: #666;
            font-size: 16px;
            margin: 20px 0;
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
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-container">
        <div class="header">
            <h1>👥 Data Pelanggan</h1>
        </div>
        
        <div class="content-card">
            @if(session('success'))
                <div style="background:#d4edda;padding:10px;border-radius:6px;margin-bottom:15px;color:#155724">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('pelanggan.store') }}" style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;justify-content:center;align-items:center;">
                @csrf
                <input type="text" name="nama_pelanggan" placeholder="Nama Pelanggan" required style="padding:10px;border-radius:6px;border:1px solid #ddd;min-width:220px">
                <input type="text" name="alamat" placeholder="Alamat (opsional)" style="padding:10px;border-radius:6px;border:1px solid #ddd;min-width:300px">
                <select name="rekening_id" style="padding:10px;border-radius:6px;border:1px solid #ddd;min-width:240px">
                    <option value="">-- Pilih Rekening (opsional) --</option>
                    @isset($rekenings)
                        @foreach($rekenings as $rek)
                            <option value="{{ $rek->id }}">{{ $rek->nomor_rekening }} {{ $rek->nama_bank ? '- ' . $rek->nama_bank : '' }}</option>
                        @endforeach
                    @endisset
                </select>
                <button type="submit" class="btn">Tambah Pelanggan</button>
            </form>

            <div style="width:100%;overflow:auto">
                <table style="width:100%;border-collapse:collapse;text-align:left">
                    <thead>
                        <tr>
                            <th style="padding:10px;border-bottom:1px solid #e8ddd4">#</th>
                            <th style="padding:10px;border-bottom:1px solid #e8ddd4">Kode</th>
                            <th style="padding:10px;border-bottom:1px solid #e8ddd4">Nama</th>
                            <th style="padding:10px;border-bottom:1px solid #e8ddd4">Alamat</th>
                            <th style="padding:10px;border-bottom:1px solid #e8ddd4">Rekening</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans ?? [] as $i => $p)
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #f0ebe6">{{ $i + 1 }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0ebe6">{{ $p->kode_pelanggan ?? ('P-' . $p->id) }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0ebe6">{{ $p->nama_pelanggan }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0ebe6">{{ $p->alamat ?? '-' }}</td>
                            <td style="padding:10px;border-bottom:1px solid #f0ebe6">{{ $p->rekening ? $p->rekening->nomor_rekening : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding:20px;text-align:center;color:#666">Belum ada data pelanggan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px;text-align:center">
                <a href="{{ route('dashboard') }}" class="btn">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
