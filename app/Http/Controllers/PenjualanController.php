<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\Pelanggan;
use App\Models\NotaHjual;
use App\Models\DetailHjual;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function create()
    {
        $barangs = Barang::all();
        $pelanggans = Pelanggan::all();
        return view('penjualan.create-multi', compact('barangs', 'pelanggans'));
    }

    public function index(Request $request)
    {
        // Ambil filter dari query string
        $filter = $request->query('filter', 'all');

        $query = NotaHjual::with(['pelanggan', 'details.barang']);

        switch ($filter) {
            case 'daily':
                $query->whereDate('tanggal', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('tanggal', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'monthly':
                $query->whereMonth('tanggal', Carbon::now()->month);
                break;
            case 'yearly':
                $query->whereYear('tanggal', Carbon::now()->year);
                break;
            default:
                // all data
                break;
        }

        $notaHjuals = $query->latest('tanggal')->get();
        $barangs = Barang::all();
        $pelanggans = Pelanggan::all();

        return view('penjualan.index', compact('notaHjuals', 'barangs', 'pelanggans'));
    }



    public function store(Request $request)
    {
        // Validate multi-item format
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barangs,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.harga_jual' => 'required|numeric|min:0',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tenggat_pembayaran' => 'nullable|date',
        ]);

        // Validate stock availability for all items
        foreach ($request->items as $item) {
            $barang = Barang::findOrFail($item['barang_id']);
            if ($barang->kuantitas < $item['quantity']) {
                return back()->withInput()->with('error', "Stok {$barang->nama} tidak mencukupi. Stok tersedia: {$barang->kuantitas}");
            }
        }

        try {
            DB::beginTransaction();

            // Generate no_nota: NJ-YYYYMMDD-XXXXX
            $today = now()->format('Ymd');
            $lastNota = NotaHjual::where('no_nota', 'like', "NJ-{$today}-%")
                ->orderBy('no_nota', 'desc')
                ->first();
            
            if ($lastNota) {
                $lastNumber = (int)substr($lastNota->no_nota, -5);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $noNota = sprintf('NJ-%s-%05d', $today, $newNumber);

            // Create nota header
            $notaHjual = NotaHjual::create([
                'no_nota' => $noNota,
                'tanggal' => now(),
                'pelanggan_id' => $request->pelanggan_id,
                'total_item' => 0, // Will be updated by trigger
                'total_harga' => 0, // Will be updated by trigger
                'status' => 'selesai',
                'keterangan' => $request->keterangan ?? null,
            ]);

            // Create detail items (triggers will auto-update totals and reduce stock)
            foreach ($request->items as $item) {
                DetailHjual::create([
                    'no_nota' => $noNota,
                    'barang_id' => $item['barang_id'],
                    'quantity' => $item['quantity'],
                    'harga_jual' => $item['harga_jual'], // Harga tersimpan per transaksi
                    'subtotal' => $item['quantity'] * $item['harga_jual'],
                ]);
            }

            DB::commit();

            // Refresh to get updated totals from triggers
            $notaHjual->refresh();

            return redirect()->route('penjualan.index')->with('success', "Nota {$noNota} berhasil dibuat dengan {$notaHjual->total_item} item senilai Rp " . number_format($notaHjual->total_harga, 0) . ".");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan penjualan: ' . $e->getMessage());
        }
    }

    public function destroy($noNota)
    {
        try {
            $notaHjual = NotaHjual::with('details')->findOrFail($noNota);
            
            // Triggers will automatically restore stock when details are deleted
            $notaHjual->delete();

            return redirect()->route('penjualan.index')->with('success', "Nota {$noNota} berhasil dibatalkan dan stok telah dikembalikan.");
        } catch (\Exception $e) {
            return redirect()->route('penjualan.index')->with('error', 'Gagal membatalkan nota: ' . $e->getMessage());
        }
    }

    public function undo()
    {
        return redirect()->route('penjualan.index')->with('error', 'Fitur undo penjualan belum tersedia.');
    }
}

