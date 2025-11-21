<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
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

        .success-message {
            background: #d1edff;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #d0c7bf;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f7f4;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #b4746f;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(180, 116, 111, 0.1);
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-update {
            background: linear-gradient(135deg, #6fa8dc, #5a8bb8);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-update:hover {
            background: linear-gradient(135deg, #5a8bb8, #4a7396);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(111, 168, 220, 0.3);
        }

        .btn-update::before {
            content: "💾";
            font-size: 16px;
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e06666, #cc5555);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #cc5555, #b84444);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(224, 102, 102, 0.3);
        }

        .btn-cancel::before {
            content: "❌";
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

        .validation-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
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

            .form-section {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-update, .btn-cancel {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-content">
        <div class="header">
            <h1>EDIT BARANG</h1>
        </div>

        @if(session('success'))
        <div class="success-message">
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="error-message">
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <div class="form-section">
            <form method="POST" action="/barang/{{ $barang->id }}">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="nama" class="form-label">Nama Barang</label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           class="form-input" 
                           value="{{ old('nama', $barang->nama) }}" 
                           placeholder="Masukkan nama barang" 
                           required>
                    @error('nama')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="harga" class="form-label">Harga</label>
                    <input type="number" 
                           id="harga" 
                           name="harga" 
                           class="form-input" 
                           value="{{ old('harga', $barang->harga) }}" 
                           placeholder="0" 
                           required 
                           min="0">
                    @error('harga')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="kuantitas" class="form-label">Kuantitas</label>
                    <input type="number" 
                           id="kuantitas" 
                           name="kuantitas" 
                           class="form-input" 
                           value="{{ old('kuantitas', $barang->kuantitas) }}" 
                           placeholder="0" 
                           required 
                           min="1">
                    @error('kuantitas')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pemasok_id" class="form-label">Pemasok</label>
                    <select id="pemasok_id" name="pemasok_id" class="form-input">
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($pemasoks as $pemasok)
                        <option value="{{ $pemasok->id }}" {{ old('pemasok_id', $barang->pemasok_id) == $pemasok->id ? 'selected' : '' }}>
                            {{ $pemasok->kode_pemasok }} - {{ $pemasok->nama_pemasok }}
                        </option>
                        @endforeach
                    </select>
                    @error('pemasok_id')
                        <div class="validation-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-update">Update Barang</button>
                    <a href="{{ route('barang.index') }}" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('barang.index') }}" class="back-btn">
        ←
    </a>
</body>
</html>