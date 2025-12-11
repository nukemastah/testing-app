<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <!-- Form Tambah Barang -->
                <form method="POST" action="/barang" class="mb-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <input type="text" name="nama" placeholder="Nama Barang" class="border p-2 rounded" required>
                        <input type="number" name="harga" placeholder="Harga Barang" class="border p-2 rounded" required>
                        <input type="number" name="kuantitas" placeholder="Kuantitas" class="border p-2 rounded" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <input type="number" name="harga_jual" placeholder="Harga Jual" class="border p-2 rounded" required>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Tambah Barang
                    </button>
                </form>

                <!-- Tabel Barang -->
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Harga</th>
                            <th class="px-4 py-2">Harga Jual</th>
                            <th class="px-4 py-2">Kuantitas</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barangs as $i => $barang)
                            <tr>
                                <td class="border px-4 py-2">{{ $i + 1 }}</td>
                                <td class="border px-4 py-2">{{ $barang->nama }}</td>
                                <td class="border px-4 py-2">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td class="border px-4 py-2">{{ $barang->kuantitas }}</td>
                                <td class="border px-4 py-2">
                                    <a href="/barang/{{ $barang->id }}/edit" class="text-blue-500 hover:underline">Edit</a>
                                    <form method="POST" action="/barang/{{ $barang->id }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


                <!-- Undo Delete -->
                @if($deletedBarang)
                    <form method="POST" action="/barang/undo" class="mt-4">
                        @csrf
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded">
                            Undo Hapus Barang Terakhir
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
