<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan - Dashboard Admin</title>
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
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .success-message {
            background: linear-gradient(135deg, #d1edff 0%, #bee5eb 100%);
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
        }

        .data-table tbody tr:nth-child(even) {
            background: linear-gradient(135deg, #fdfcfa 0%, #f9f7f4 100%);
        }

        .data-table tbody tr:hover {
            background: linear-gradient(135deg, #f5f1eb 0%, #ede7de 100%);
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
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

        .btn-delete {
            background: linear-gradient(135deg, #e06666, #cc5555);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #cc5555, #b84444);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(224, 102, 102, 0.3);
        }

        .form-section {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.1);
            border: 1px solid #e8ddd4;
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
            font-size: 13px;
        }

        .form-input, .form-textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #d0c7bf;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f9f7f4;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: #b4746f;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(180, 116, 111, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #e06666, #cc5555);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: linear-gradient(135deg, #cc5555, #b84444);
        }

        .section-title {
            font-size: 1.8rem;
            color: #333;
            font-weight: bold;
            margin: 40px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .no-data {
            padding: 40px;
            text-align: center;
            color: #888;
            font-style: italic;
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
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: linear-gradient(135deg, #ffffff 0%, #fdfcfa 100%);
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        .close-btn {
            font-size: 28px;
            font-weight: bold;
            color: #999;
            cursor: pointer;
            float: right;
        }

        .close-btn:hover {
            color: #333;
        }

        .modal-footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    @include('components.sidebar')

    <div class="main-content">
        <div class="header">
            <h1>👥 DATA PELANGGAN</h1>
        </div>

        @if(session('success'))
        <div class="success-message">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>KODE</th>
                        <th>NAMA PELANGGAN</th>
                        <th>ALAMAT</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggans as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->kode_pelanggan ?? 'P-' . str_pad($p->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $p->nama_pelanggan }}</td>
                        <td>{{ $p->alamat ?? '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-edit" type="button" data-id="{{ $p->id }}" data-nama="{{ $p->nama_pelanggan }}" data-alamat="{{ $p->alamat ?? '' }}" onclick="const btn = this; editPelanggan(btn.dataset.id, btn.dataset.nama, btn.dataset.alamat)">Edit</button>
                                <form method="POST" action="{{ route('pelanggan.destroy', $p->id) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="no-data">Belum ada data pelanggan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h2 class="section-title">TAMBAH PELANGGAN</h2>

        <div class="form-section">
            <form method="POST" action="{{ route('pelanggan.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" class="form-input" placeholder="Masukkan nama pelanggan" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat (opsional)</label>
                    <textarea name="alamat" class="form-textarea" placeholder="Masukkan alamat pelanggan"></textarea>
                </div>

                <button type="submit" class="btn-submit">Tambah Pelanggan</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                Edit Pelanggan
                <span class="close-btn" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" id="editNama" name="nama_pelanggan" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat (opsional)</label>
                    <textarea id="editAlamat" name="alamat" class="form-textarea"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="back-btn">←</a>

    <script>
        function editPelanggan(id, nama, alamat) {
            document.getElementById('editNama').value = nama;
            document.getElementById('editAlamat').value = alamat;
            document.getElementById('editForm').action = "{{ route('pelanggan.index') }}/" + id;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
