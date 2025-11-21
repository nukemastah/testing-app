<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemasok - Dashboard Admin</title>
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
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2rem;
            color: #333;
            font-weight: bold;
        }

        .btn-keluar {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-keluar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .search-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #b4746f;
        }

        .search-btn {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-tambah {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .data-table thead {
            background: linear-gradient(135deg, #f0ebe6 0%, #e8ddd4 100%);
        }

        .data-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #d0c7bf;
            font-size: 14px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 15px 12px;
            text-align: left;
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

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            gap: 10px;
        }

        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .pagination-btn:hover {
            background: #f0ebe6;
        }

        .pagination-btn.active {
            background: #b4746f;
            color: white;
            border-color: #b4746f;
        }

        .no-data {
            padding: 40px 20px;
            text-align: center;
            color: #888;
            font-style: italic;
        }

        .action-buttons {
            display: none;
            gap: 10px;
            align-items: center;
        }

        .action-buttons.show {
            display: flex;
        }

        .btn-edit, .btn-hapus {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
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
        }

        .btn-hapus {
            background: linear-gradient(135deg, #e06666, #cc5555);
            color: white;
        }

        .btn-hapus:hover {
            background: linear-gradient(135deg, #cc5555, #b84444);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #b4746f;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-save, .btn-cancel {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-save:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
        }

        .btn-cancel {
            background: #ccc;
            color: #333;
        }

        .btn-cancel:hover {
            background: #bbb;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .header {
                flex-direction: column;
                gap: 15px;
            }

            .search-section {
                flex-direction: column;
            }

            .data-table {
                font-size: 12px;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    @include('components.sidebar')
    
    <div class="main-content">
        <div class="header">
            <h1>Pemasok</h1>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-keluar">Keluar</button>
            </form>
        </div>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Cari Data" id="searchInput">
            <button class="search-btn" onclick="filterTable()">🔍</button>
        </div>

        <div class="action-bar">
            <div style="display: flex; gap: 15px; align-items: center; width: 100%;">
                <button class="btn-tambah" onclick="openAddModal()">Tambah +</button>
                <div class="action-buttons" id="actionButtons">
                    <button class="btn-edit" onclick="editSelected()">Edit</button>
                    <button class="btn-hapus" onclick="deleteSelected()">Hapus</button>
                </div>
            </div>
            <div class="pagination-info">Halaman 1 dari 1</div>
        </div>

        <div class="table-container">
            <table class="data-table" id="pemasokTable">
                <thead>
                    <tr>
                        <th style="width: 5%;"><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                        <th>Kode Pemasok</th>
                        <th>Nama Pemasok</th>
                        <th>Alamat Pemasok</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($pemasoks as $item)
                    <tr data-id="{{ $item->id }}">
                        <td><input type="checkbox" class="row-checkbox" data-id="{{ $item->id }}" onchange="updateActionButtons()"></td>
                        <td>{{ $item->kode_pemasok }}</td>
                        <td>{{ $item->nama_pemasok }}</td>
                        <td>{{ $item->alamat_pemasok }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="no-data">Tidak ada data pemasok</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <button class="pagination-btn" disabled>&larr;</button>
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn" disabled>&rarr;</button>
        </div>
    </div>

    <!-- Modal Tambah/Edit Pemasok -->
    <div class="modal" id="pemasokModal">
        <div class="modal-content">
            <div class="modal-header" id="modalTitle">Tambah Pemasok</div>
            <form id="pemasokForm" onsubmit="savePemasok(event)">
                <div class="form-group" id="kodeGroup" style="display: none;">
                    <label for="kodePemasok">Kode Pemasok (Auto)</label>
                    <input type="text" id="kodePemasok" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                </div>
                <div class="form-group">
                    <label for="namaPemasok">Nama Pemasok</label>
                    <input type="text" id="namaPemasok" placeholder="Nama pemasok" required>
                </div>
                <div class="form-group">
                    <label for="alamatPemasok">Alamat Pemasok</label>
                    <input type="text" id="alamatPemasok" placeholder="Alamat lengkap" required>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingId = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        function updateActionButtons() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            const actionButtons = document.getElementById('actionButtons');
            if (checkboxes.length > 0) {
                actionButtons.classList.add('show');
            } else {
                actionButtons.classList.remove('show');
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateActionButtons();
        }

        function openAddModal() {
            editingId = null;
            document.getElementById('modalTitle').textContent = 'Tambah Pemasok';
            document.getElementById('kodeGroup').style.display = 'none';
            document.getElementById('pemasokForm').reset();
            document.getElementById('pemasokModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('pemasokModal').classList.remove('show');
        }

        function savePemasok(e) {
            e.preventDefault();
            const nama = document.getElementById('namaPemasok').value;
            const alamat = document.getElementById('alamatPemasok').value;

            const data = {
                nama_pemasok: nama,
                alamat_pemasok: alamat
            };

            const url = editingId ? `/pemasok/${editingId}` : '/pemasok';
            const method = editingId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan: ' + error.message);
            });

            closeModal();
        }

        function editSelected() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length !== 1) {
                alert('Pilih satu data untuk diedit');
                return;
            }

            const row = checkboxes[0].closest('tr');
            editingId = checkboxes[0].dataset.id;
            
            document.getElementById('modalTitle').textContent = 'Edit Pemasok';
            document.getElementById('kodeGroup').style.display = 'block';
            document.getElementById('kodePemasok').value = row.cells[1].textContent;
            document.getElementById('namaPemasok').value = row.cells[2].textContent;
            document.getElementById('alamatPemasok').value = row.cells[3].textContent;
            document.getElementById('pemasokModal').classList.add('show');
        }

        function deleteSelected() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) {
                alert('Pilih data untuk dihapus');
                return;
            }

            if (confirm(`Hapus ${checkboxes.length} data?`)) {
                const ids = Array.from(checkboxes).map(cb => cb.dataset.id);
                
                ids.forEach(id => {
                    fetch(`/pemasok/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) row.remove();
                        }
                    });
                });
                
                document.getElementById('selectAll').checked = false;
                updateActionButtons();
                setTimeout(() => location.reload(), 500);
            }
        }

        function filterTable() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            rows.forEach(row => {
                if (row.cells.length > 1) {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', filterTable);

        // Close modal when clicking outside
        document.getElementById('pemasokModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Add CSRF token to document if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    </script>
</body>
</html>
