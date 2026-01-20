<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\UnitsImport;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel; // Nanti kita buat ini

class UnitController extends Controller
{
    public function index(Request $request)
    {
        // Fitur Search
        $query = Unit::query();
        if ($request->search) {
            $query->where('unit_number', 'like', '%'.$request->search.'%');
        }

        $units = $query->orderBy('unit_number', 'asc')->paginate(10);

        return Inertia::render('admin/units/Index', [
            'units' => $units,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'unit_number' => 'required|unique:units,unit_number',
            'owner_name' => 'nullable|string',
            'access_code' => 'nullable|string|min:4', // PIN boleh kosong
        ]);

        // Jika PIN diisi admin -> Pakai itu
        // Jika kosong -> Pakai default '123456'
        $pin = $request->access_code ? $request->access_code : '123456';

        Unit::create([
            'unit_number' => strtoupper($request->unit_number),
            'owner_name' => $request->owner_name,
            'access_code' => Hash::make($pin), // Hash PIN
            'is_active' => true,
        ]);

        return back()->with('success', 'Unit berhasil ditambahkan.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate(['owner_name' => 'nullable|string']);

        $unit->update(['owner_name' => $request->owner_name]);

        return back()->with('success', 'Data unit diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return back()->with('success', 'Unit dihapus.');
    }

    // RESET PIN KE DEFAULT (123456)
    public function resetPin(Unit $unit)
    {
        $unit->update([
            'access_code' => Hash::make('123456'),
        ]);

        return back()->with('success', 'PIN Unit '.$unit->unit_number.' direset ke 123456');
    }

    // Method baru: UPDATE PIN MANUAL
    public function updatePin(Request $request, Unit $unit)
    {
        $request->validate([
            'new_pin' => 'required|string|min:4',
        ]);

        $unit->update([
            'access_code' => Hash::make($request->new_pin),
        ]);

        return back()->with('success', 'PIN Unit '.$unit->unit_number.' berhasil diubah.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            // Panggil class Import yang baru kita buat
            Excel::import(new UnitsImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data unit berhasil diimport!');
        } catch (\Exception $e) {
            // Tangkap error jika format excel salah
            return redirect()->back()->withErrors(['file' => 'Gagal import: '.$e->getMessage()]);
        }
    }
}
