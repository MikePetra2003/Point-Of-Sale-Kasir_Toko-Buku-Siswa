<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangSatuan;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $barang = Barang::with(['kategori', 'satuan', 'supplier', 'barangSatuan.satuan'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('kode_barang', 'like', "%{$keyword}%")
                    ->orWhere('nama_barang', 'like', "%{$keyword}%");
            })
            ->latest()
            ->paginate(10);

        return view('barang.index', compact('barang', 'keyword'));
    }

    public function create()
    {
        $kategori = Kategori::orderBy('id')->get();
        $satuan = Satuan::orderBy('nama_satuan')->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();

        return view('barang.create', compact('kategori', 'satuan', 'supplier'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_barang' => 'required|string|max:20|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id' => 'required|exists:satuan,id',
            'supplier_id' => 'nullable|exists:supplier,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan_tambahan' => 'nullable|array',
            'satuan_tambahan.*.satuan_id' => 'nullable|exists:satuan,id',
            'satuan_tambahan.*.konversi_ke_satuan_dasar' => 'nullable|integer|min:2',
            'satuan_tambahan.*.harga_beli' => 'nullable|numeric|min:0',
            'satuan_tambahan.*.harga_jual' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            $barang = Barang::create(collect($data)->only([
                'kode_barang',
                'nama_barang',
                'kategori_id',
                'satuan_id',
                'supplier_id',
                'harga_beli',
                'harga_jual',
                'stok',
            ])->all());

            $this->syncBarangSatuan($barang, $request->input('satuan_tambahan', []));
        });

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        $kategori = Kategori::orderBy('id')->get();
        $barang->load('barangSatuan.satuan');
        $satuan = Satuan::orderBy('nama_satuan')->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();

        return view('barang.edit', compact('barang', 'kategori', 'satuan', 'supplier'));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'kode_barang' => 'required|string|max:20|unique:barang,kode_barang,'.$barang->id,
            'nama_barang' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id' => 'required|exists:satuan,id',
            'supplier_id' => 'nullable|exists:supplier,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan_tambahan' => 'nullable|array',
            'satuan_tambahan.*.satuan_id' => 'nullable|exists:satuan,id',
            'satuan_tambahan.*.konversi_ke_satuan_dasar' => 'nullable|integer|min:2',
            'satuan_tambahan.*.harga_beli' => 'nullable|numeric|min:0',
            'satuan_tambahan.*.harga_jual' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($barang, $data, $request) {
            $barang->update(collect($data)->only([
                'kode_barang',
                'nama_barang',
                'kategori_id',
                'satuan_id',
                'supplier_id',
                'harga_beli',
                'harga_jual',
                'stok',
            ])->all());

            $this->syncBarangSatuan($barang, $request->input('satuan_tambahan', []));
        });

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        
        $sudahDipakaiTransaksi = $barang->detailPembelian()->exists()
        || $barang->detailPenjualan()->exists();

        
    if ($sudahDipakaiTransaksi) {
        $barang->update(['is_active' => false]);


        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang sudah pernah dipakai transaksi, jadi dinonaktifkan.');
    } 

        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }

    private function syncBarangSatuan(Barang $barang, array $satuanTambahan): void
    {
        $barang->barangSatuan()->delete();

        BarangSatuan::create([
            'barang_id' => $barang->id,
            'satuan_id' => $barang->satuan_id,
            'konversi_ke_satuan_dasar' => 1,
            'harga_beli' => $barang->harga_beli,
            'harga_jual' => $barang->harga_jual,
            'is_satuan_dasar' => true,
        ]);

        collect($satuanTambahan)
            ->filter(fn ($row) => filled($row['satuan_id'] ?? null))
            ->reject(fn ($row) => (int) $row['satuan_id'] === (int) $barang->satuan_id)
            ->unique(fn ($row) => (int) $row['satuan_id'])
            ->each(function ($row) use ($barang) {
                $konversi = (int) ($row['konversi_ke_satuan_dasar'] ?? 0);

                if ($konversi < 2) {
                    return;
                }

                BarangSatuan::create([
                    'barang_id' => $barang->id,
                    'satuan_id' => (int) $row['satuan_id'],
                    'konversi_ke_satuan_dasar' => $konversi,
                    'harga_beli' => (float) ($row['harga_beli'] ?? 0) ?: (float) $barang->harga_beli * $konversi,
                    'harga_jual' => (float) ($row['harga_jual'] ?? 0) ?: (float) $barang->harga_jual * $konversi,
                    'is_satuan_dasar' => false,
                ]);
            });
    }
}
