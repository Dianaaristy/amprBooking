<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
        $endDate = Carbon::now('Asia/Jakarta')->addDays(29)->endOfDay();

        // 1. Generate Header Tanggal
        $days = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $days[] = [
                'date' => $current->format('Y-m-d'),
                'day_name' => $current->locale('id')->isoFormat('dddd'),
                'formatted' => $current->format('d M'),
                'is_today' => $current->isToday(),
            ];
            $current->addDay();
        }

        // 2. Ambil Booking & Maintenance
        $bookings = Booking::with('unit')
            ->whereBetween('start_time', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(function ($book) {
                $status = $book->status;

                // Logika No Show
                if ($status === 'booked' && Carbon::parse($book->start_time)->lt(now())) {
                    $status = 'no_show';
                }

                // --- PERBAIKAN LOGIKA DESKRIPSI MAINTENANCE ---
                // Data disimpan sebagai JSON ["Alasan"] di kolom player_names
                $description = null;
                if ($book->status === 'maintenance') {
                    // Coba decode JSON
                    $decoded = json_decode($book->player_names, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        // Jika berhasil decode dan array, ambil elemen pertama
                        $description = $decoded[0] ?? 'Maintenance';
                    } else {
                        // Jika bukan JSON (string biasa), pakai langsung
                        $description = $book->player_names ?? 'Maintenance';
                    }
                }

                return [
                    'id' => $book->id,
                    'unit' => $book->unit->unit_number ?? 'Unknown',
                    'date' => Carbon::parse($book->start_time)->format('Y-m-d'),
                    'hour' => (int) Carbon::parse($book->start_time)->format('H'),
                    'status' => $status,
                    'description' => $description, // Deskripsi dinamis
                ];
            });

        // 3. Stats
        $stats = [
            'total_units' => Unit::count(),
            'banned_units' => Unit::where('is_banned_until', '>', now())->count(),
            'bookings_today' => Booking::whereDate('start_time', today())->count(),
        ];

        // 4. Data Unit untuk Dropdown (Pastikan query ini benar)
        $units = Unit::select('id', 'unit_number', 'owner_name')
            ->orderBy('unit_number')
            ->get();

        return Inertia::render('admin/Dashboard', [
            'days' => $days,
            'bookings' => $bookings,
            'stats' => $stats,
            'units' => $units, // Kirim ke Vue
        ]);
    }

    // ... method store (tetap sama) ...
    public function store(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'date' => 'required|date',
            'hour' => 'required|integer|min:6|max:22',
        ]);

        $startTime = Carbon::parse($request->date)->setHour($request->hour)->setMinute(0)->setSecond(0);
        $endTime = $startTime->copy()->addHour();

        // 1. Validasi Waktu Lampau
        if ($startTime->lt(now())) {
            return back()->withErrors(['modal' => 'Gagal! Tidak bisa booking waktu yang sudah lewat.']);
        }

        // 2. Cek Ketersediaan
        $exists = Booking::where('start_time', $startTime)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->withErrors(['modal' => 'Slot waktu ini sudah terisi!']);
        }

        // 3. Simpan
        Booking::create([
            'unit_id' => $request->unit_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'booked',
            'booking_code' => strtoupper(Str::random(8)),
            'player_names' => json_encode(['Admin Booking']),
        ]);

        return back()->with('success', 'Booking berhasil ditambahkan.');
    }
}
