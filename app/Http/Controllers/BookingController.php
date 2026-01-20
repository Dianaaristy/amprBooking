<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\TicketResource;
use App\Models\Booking;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BookingController extends Controller
{
    /**
     * PAGE: Halaman Utama (Jadwal)
     */
    public function index()
    {
        // PENTING: Gunakan Timezone Jakarta
        $today = Carbon::now('Asia/Jakarta');

        // --- 1. FITUR DYNAMIC DATE (1 BULAN) ---
        $endDate = $today->copy()->addMonth();
        $daysDifference = $today->diffInDays($endDate);

        $dates = collect(range(0, $daysDifference))->map(function ($days) use ($today) {
            $date = $today->copy()->addDays($days);

            return [
                'full_date' => $date->format('Y-m-d'),
                'day_name' => $date->locale('id')->isoFormat('ddd'), // Sen, Sel
                'date_num' => $date->format('d'),
                'month_name' => $date->locale('id')->isoFormat('MMM'), // Des
                'is_today' => $date->isToday(),
            ];
        });

        // 2. Ambil Data Slot Terisi
        $rawBookings = Booking::active()
            ->whereDate('start_time', '>=', $today->format('Y-m-d'))
            ->whereDate('start_time', '<=', $endDate->format('Y-m-d'))
            ->get(['start_time']);

        $bookedSlots = $rawBookings->map(function ($b) {
            return [
                'date' => $b->start_time->format('Y-m-d'),
                'hour' => (int) $b->start_time->format('H'),
            ];
        });

        // 3. Logic Tanggal Penuh (16 Slot per hari)
        $bookingsPerDate = $bookedSlots->groupBy('date')->map->count();
        $fullyBookedDates = $bookingsPerDate->filter(fn ($count) => $count >= 16)->keys()->toArray();

        // [REVISI] Ambil Nomor Unit dari Session untuk ditampilkan di Header Frontend
        // Tidak perlu $units dropdown lagi
        $userUnit = session('resident_unit_number', 'Unknown Unit');

        return Inertia::render('booking/Home', [
            'dates' => $dates,
            'bookedSlots' => $bookedSlots,
            // 'units' => $units, <-- SUDAH TIDAK DIPAKAI
            'fullyBookedDates' => $fullyBookedDates,
            'userUnit' => $userUnit, // <-- KIRIM KE FRONTEND
        ]);
    }

    /**
     * ACTION: Proses Booking (Logic Validasi Lengkap)
     */
    public function store(StoreBookingRequest $request)
    {
        // Setup Waktu (Timezone Jakarta)
        $startTime = Carbon::createFromFormat('Y-m-d H', "$request->date $request->hour", 'Asia/Jakarta');
        $endTime = $startTime->copy()->addHour();

        // --- VALIDASI 3: CONCURRENCY (ATOMIC LOCK) ---
        $lockKey = "booking_lock_{$startTime->timestamp}";
        $lock = Cache::lock($lockKey, 5); // 5 detik

        if (! $lock->get()) {
            throw ValidationException::withMessages(['slot' => 'Slot sedang diproses oleh penghuni lain. Silakan coba lagi.']);
        }

        try {
            return DB::transaction(function () use ($request, $startTime, $endTime) {

                // [REVISI] 1. AMBIL UNIT DARI SESSION (BUKAN INPUT MANUAL)
                $unitId = session('resident_unit_id');
                $unit = Unit::find($unitId);

                // Validasi Session (Jaga-jaga kalau expired pas lagi klik)
                if (! $unit) {
                    throw ValidationException::withMessages(['player_names' => 'Sesi Anda habis. Silakan login ulang.']);
                }

                // --- VALIDASI 1: SANKSI (BANNED) ---
                if ($unit->is_banned_until && now() < $unit->is_banned_until) {
                    $dateEnd = Carbon::parse($unit->is_banned_until)->format('d M Y');
                    throw ValidationException::withMessages([
                        'player_names' => "Unit Anda sedang disanksi (Banned) hingga {$dateEnd}. Tidak dapat melakukan booking.",
                    ]);
                }

                // --- VALIDASI 2: KUOTA MINGGUAN ---
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();

                $weeklyUsage = Booking::active()
                    ->where('unit_id', $unit->id)
                    ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
                    ->count();

                if ($weeklyUsage >= 2) {
                    throw ValidationException::withMessages([
                        'player_names' => 'Kuota mingguan Anda habis. Maksimal 2 jam per minggu.',
                    ]);
                }

                // 2. Cek Ketersediaan Slot (Double Check Database)
                $isBooked = Booking::active()
                    ->where('start_time', $startTime->format('Y-m-d H:i:s'))
                    ->exists();

                if ($isBooked) {
                    throw ValidationException::withMessages(['slot' => 'Maaf, slot ini baru saja diambil orang lain.']);
                }

                // 3. Simpan Booking
                $booking = Booking::create([
                    'unit_id' => $unit->id, // [REVISI] Pakai ID Session
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'player_names' => [$request->player_names],
                    'status' => 'booked',
                ]);

                // Generate Booking Code jika Observer macet
                if (! $booking->booking_code) {
                    $booking->booking_code = 'TN-'.strtoupper(uniqid());
                    $booking->save();
                }

                // Update Counter Unit
                $unit->increment('quota_usage');

                // 4. Redirect ke Halaman Sukses
                return to_route('booking.ticket', $booking->booking_code);
            });

        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }

    public function showTicket(Booking $booking)
    {
        $booking->load('unit');

        // Pastikan tiket milik unit yang sedang login (Security Check)
        if (session('resident_unit_id') && $booking->unit_id != session('resident_unit_id')) {
            abort(403, 'Anda tidak berhak melihat tiket ini.');
        }

        $qrCode = (new QRCode)->render($booking->booking_code);

        return Inertia::render('booking/Success', [
            'booking' => $booking,
            'qrCode' => $qrCode,
        ]);
    }

    public function downloadPdf(Booking $booking)
    {
        // Security check sama seperti showTicket
        if (session('resident_unit_id') && $booking->unit_id != session('resident_unit_id')) {
            abort(403);
        }

        $booking->load('unit');

        $options = new \chillerlan\QRCode\QROptions([
            'version' => 5,
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
        ]);

        $qrCode = (new QRCode($options))->render($booking->booking_code);

        $pdf = Pdf::loadView('pdf.ticket', [
            'booking' => $booking,
            'qrCode' => $qrCode,
        ]);

        $pdf->setPaper('A5', 'portrait');
        $pdf->setWarnings(false);

        return $pdf->download('e-ticket-'.$booking->booking_code.'.pdf');
    }

    public function myTickets()
    {
        $unitId = session('resident_unit_id');
        $userUnit = session('resident_unit_number');

        // 1. Tiket AKAN DATANG
        $activeData = Booking::where('unit_id', $unitId)
            ->where('start_time', '>=', now())
            ->whereIn('status', ['booked'])
            ->orderBy('start_time', 'asc')
            ->get();

        // 2. Tiket RIWAYAT
        $historyData = Booking::where('unit_id', $unitId)
            ->where(function ($q) {
                $q->where('start_time', '<', now())
                    ->orWhereIn('status', ['checked_in', 'cancelled', 'no_show']);
            })
            ->orderBy('start_time', 'desc')
            ->limit(20)
            ->get();

        // RETURN MENGGUNAKAN RESOURCE
        // TicketResource::collection(...) otomatis mengolah data sesuai format di file Resource tadi
        return Inertia::render('booking/MyTickets', [
            'activeTickets' => TicketResource::collection($activeData),
            'historyTickets' => TicketResource::collection($historyData),
            'userUnit' => $userUnit,
        ]);
    }
}
