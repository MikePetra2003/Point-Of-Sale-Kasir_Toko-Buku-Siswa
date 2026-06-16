<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $barang = Barang::with(['kategori', 'satuan', 'supplier'])
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
        $satuan = Satuan::whereIn('nama_satuan', ['pcs', 'pack'])->orderBy('id')->get();
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
        ]);

        Barang::create($data);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang)
    {
        $kategori = Kategori::orderBy('id')->get();
        $satuan = Satuan::whereIn('nama_satuan', ['pcs', 'pack'])->orderBy('id')->get();
        $supplier = Supplier::orderBy('nama_supplier')->get();

        return view('barang.edit', compact('barang', 'kategori', 'satuan', 'supplier'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:20|unique:barang,kode_barang,'.$barang->id,
            'nama_barang' => 'required|string|max:100',
            'kategori_id' => 'required|exists:kategori,id',
            'satuan_id' => 'required|exists:satuan,id',
            'supplier_id' => 'nullable|exists:supplier,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $barang->update($request->only([
            'kode_barang',
            'nama_barang',
            'kategori_id',
            'satuan_id',
            'supplier_id',
            'harga_beli',
            'harga_jual',
            'stok',
        ]));

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}
