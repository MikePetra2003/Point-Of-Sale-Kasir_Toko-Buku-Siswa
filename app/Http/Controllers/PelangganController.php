<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $pelanggan = Pelanggan::when($keyword, function ($query) use ($keyword) {
            $query->where('nama_pelanggan', 'like', "%{$keyword}%")
                ->orWhere('no_id_pelanggan', 'like', "%{$keyword}%")
                ->orWhere('no_telepon', 'like', "%{$keyword}%");
        })
            ->latest()
            ->paginate(10);

        return view('pelanggan.index', compact('pelanggan', 'keyword'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $validated['nama_pelanggan'] = trim($validated['nama_pelanggan']);

        if ($this->namaPelangganSudahDipakai($validated['nama_pelanggan'])) {
            return back()
                ->withInput()
                ->withErrors(['nama_pelanggan' => 'Nama pelanggan sudah dipakai oleh pelanggan lain.']);
        }

        DB::transaction(function () use ($validated) {
            Pelanggan::create($validated);
        });

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $validated['nama_pelanggan'] = trim($validated['nama_pelanggan']);

        if ($this->namaPelangganSudahDipakai($validated['nama_pelanggan'], $pelanggan->id)) {
            return back()
                ->withInput()
                ->withErrors(['nama_pelanggan' => 'Nama pelanggan sudah dipakai oleh pelanggan lain.']);
        }

        $pelanggan->update($validated);

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        abort_unless(auth()->user()?->role === 'owner', 403);

        $pelanggan->delete();

        return redirect()
            ->route('pelanggan.index')
            ->with('success', 'Data pelanggan berhasil dihapus.');
    }

    private function namaPelangganSudahDipakai(string $namaPelanggan, ?int $ignoreId = null): bool
    {
        return Pelanggan::whereRaw('LOWER(nama_pelanggan) = ?', [Str::lower($namaPelanggan)])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }
}
