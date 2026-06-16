<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $supplier = Supplier::when($keyword, function ($query) use ($keyword) {
            $query->where('nama_supplier', 'like', "%{$keyword}%")
                ->orWhere('no_telepon', 'like', "%{$keyword}%")
                ->orWhere('alamat', 'like', "%{$keyword}%");
        })
            ->latest()
            ->paginate(10);

        return view('supplier.index', compact('supplier', 'keyword'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        Supplier::create($request->only([
            'nama_supplier',
            'no_telepon',
            'alamat',
        ]));

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Data supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $supplier->update($request->only([
            'nama_supplier',
            'no_telepon',
            'alamat',
        ]));

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('supplier.index')
            ->with('success', 'Data supplier berhasil dihapus.');
    }
}
