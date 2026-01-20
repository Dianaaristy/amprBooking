<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// [REVISI] Props menerima Object Resource
const props = defineProps({
    activeTickets: Object,
    historyTickets: Object,
    userUnit: String,
});

// [REVISI] Ambil data dari dalam wrapper Resource (.data)
// Tambahkan '|| []' untuk mencegah error jika data kosong
const activeList = props.activeTickets?.data || [];
const historyList = props.historyTickets?.data || [];

const activeTab = ref('active');
const showQrModal = ref(false);
const currentQrCode = ref('');
const currentBookingCode = ref('');

// [REVISI] Langsung terima gambar Base64 dari Backend
// Tidak perlu library 'qrcode' JS lagi
const openQrModal = (code, base64Image) => {
    currentBookingCode.value = code;
    currentQrCode.value = base64Image;
    showQrModal.value = true;
};

// ... (Sisa helper formatDate, formatTime, getStatusBadge TETAP SAMA) ...
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatTime = (start, end) => {
    const s = new Date(start).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
    const e = new Date(end).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
    return `${s} - ${e}`;
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'booked':
            return {
                text: 'Booked',
                class: 'bg-blue-100 text-blue-700 border border-blue-200',
            };
        case 'checked_in':
            return {
                text: 'Sudah Check-in',
                class: 'bg-green-100 text-green-700 border border-green-200',
            };
        case 'no_show':
            return {
                text: 'Hangus / No Show',
                class: 'bg-red-100 text-red-700 border border-red-200',
            };
        case 'cancelled':
            return {
                text: 'Dibatalkan',
                class: 'bg-gray-100 text-gray-500 border border-gray-200',
            };
        default:
            return { text: status, class: 'bg-gray-100 text-gray-600' };
    }
};
</script>

<template>
    <Head title="Tiket Saya" />

    <div
        class="flex h-[100dvh] justify-center overflow-hidden bg-[#E7E5D7] font-sans text-slate-800 antialiased"
    >
        <div
            class="relative flex h-full w-full max-w-[480px] flex-col overflow-hidden border-x border-[#869A69]/20 bg-white shadow-2xl"
        >
            <!-- HEADER -->
            <div class="z-10 flex-none bg-white px-6 pt-10 pb-4 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-black text-[#65AAC2]">
                        Tiket Saya
                    </h1>
                    <div class="rounded-full bg-[#E7E5D7] px-3 py-1">
                        <span class="text-[10px] font-bold text-[#869A69]">{{
                            userUnit
                        }}</span>
                    </div>
                </div>

                <!-- TAB SWITCHER -->
                <div class="flex rounded-xl bg-[#F3F4F6] p-1">
                    <button
                        @click="activeTab = 'active'"
                        class="flex-1 rounded-lg py-2.5 text-xs font-bold tracking-wider uppercase transition-all duration-300"
                        :class="
                            activeTab === 'active'
                                ? 'bg-white text-[#65AAC2] shadow-sm'
                                : 'text-gray-400 hover:text-gray-600'
                        "
                    >
                        Akan Datang ({{ activeList.length }})
                    </button>
                    <button
                        @click="activeTab = 'history'"
                        class="flex-1 rounded-lg py-2.5 text-xs font-bold tracking-wider uppercase transition-all duration-300"
                        :class="
                            activeTab === 'history'
                                ? 'bg-white text-[#65AAC2] shadow-sm'
                                : 'text-gray-400 hover:text-gray-600'
                        "
                    >
                        Riwayat
                    </button>
                </div>
            </div>

            <!-- SCROLLABLE LIST -->
            <main
                class="no-scrollbar flex-1 overflow-y-auto bg-[#FAFAFA] px-6 pt-6 pb-24"
            >
                <!-- LIST ACTIVE (Gunakan activeList) -->
                <div v-if="activeTab === 'active'" class="space-y-4">
                    <div
                        v-if="activeList.length === 0"
                        class="flex flex-col items-center justify-center py-10 opacity-60"
                    >
                        <!-- Icon Empty -->
                        <svg
                            class="mb-3 h-16 w-16 text-gray-300"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                            ></path>
                        </svg>
                        <p class="text-sm font-bold text-gray-400">
                            Belum ada tiket aktif
                        </p>
                        <Link
                            href="/booking"
                            class="mt-2 text-xs font-bold text-[#65AAC2] hover:underline"
                            >Booking Sekarang</Link
                        >
                    </div>

                    <div
                        v-for="ticket in activeList"
                        :key="ticket.id"
                        class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-md transition hover:shadow-lg"
                    >
                        <div
                            class="absolute top-0 bottom-0 left-0 w-1.5 bg-[#65AAC2]"
                        ></div>
                        <div class="p-5 pl-7">
                            <div class="mb-4 flex items-start justify-between">
                                <div>
                                    <p
                                        class="mb-1 text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                                    >
                                        Jadwal Main
                                    </p>
                                    <h3
                                        class="text-lg font-black text-slate-700"
                                    >
                                        {{ formatDate(ticket.start_time) }}
                                    </h3>
                                    <p class="text-sm font-bold text-[#65AAC2]">
                                        {{
                                            formatTime(
                                                ticket.start_time,
                                                ticket.end_time,
                                            )
                                        }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="inline-block rounded-md bg-blue-50 px-2 py-1 text-[10px] font-bold tracking-wide text-blue-600 uppercase"
                                    >
                                        {{ ticket.booking_code }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 border-t border-dashed border-gray-200 pt-4"
                            >
                                <!-- [REVISI] KIRIM GAMBAR QR KE MODAL -->
                                <button
                                    @click="
                                        openQrModal(
                                            ticket.booking_code,
                                            ticket.qr_code_image,
                                        )
                                    "
                                    class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#65AAC2] py-2.5 text-xs font-bold text-white transition hover:bg-[#528ea3]"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 6h6v6H6V6zm12 0h6v6h-6V6zm-6 12h6v6h-6v-6z"
                                        ></path>
                                    </svg>
                                    Lihat QR
                                </button>
                                <a
                                    :href="`/ticket/${ticket.booking_code}/pdf`"
                                    target="_blank"
                                    class="flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-xs font-bold text-gray-600 hover:bg-gray-50"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                        ></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LIST HISTORY (Gunakan historyList) -->
                <div v-if="activeTab === 'history'" class="space-y-4">
                    <div
                        v-if="historyList.length === 0"
                        class="py-10 text-center text-xs font-bold text-gray-400"
                    >
                        Belum ada riwayat booking
                    </div>

                    <div
                        v-for="ticket in historyList"
                        :key="ticket.id"
                        class="relative rounded-2xl border bg-white p-5 transition-all"
                        :class="
                            ticket.status === 'checked_in'
                                ? 'border-green-200 bg-green-50/30 shadow-sm'
                                : 'border-gray-100 opacity-70'
                        "
                    >
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-600">
                                {{ formatDate(ticket.start_time) }}
                            </h3>
                            <div
                                class="flex items-center gap-1.5 rounded-lg px-2 py-1 text-[10px] font-bold tracking-wide uppercase"
                                :class="getStatusBadge(ticket.status).class"
                            >
                                <svg
                                    v-if="ticket.status === 'checked_in'"
                                    class="h-3 w-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="3"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <svg
                                    v-if="ticket.status === 'no_show'"
                                    class="h-3 w-3"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12"
                                    ></path>
                                </svg>
                                {{ getStatusBadge(ticket.status).text }}
                            </div>
                        </div>
                        <p class="text-xs font-medium text-gray-400">
                            {{ formatTime(ticket.start_time, ticket.end_time) }}
                        </p>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="font-mono text-[10px] text-gray-400"
                                >#{{ ticket.booking_code }}</span
                            >
                            <a
                                :href="`/ticket/${ticket.booking_code}/pdf`"
                                target="_blank"
                                class="text-[10px] font-bold text-[#65AAC2] hover:underline"
                                >Download Ulang</a
                            >
                        </div>
                    </div>
                </div>
            </main>

            <!-- BAGIAN 3: BOTTOM NAVIGATION (Floating Glass) -->
            <div class="absolute right-6 bottom-6 left-6 z-30">
                <nav
                    class="flex items-center justify-between rounded-[2rem] border border-white/50 bg-gradient-to-br from-[#E7E5D7]/90 via-white/90 px-6 py-3 shadow-xl shadow-[#65AAC2]/10 backdrop-blur-lg"
                >
                    <!-- 1. HOME -->
                    <Link
                        href="/booking"
                        class="flex w-12 flex-col items-center gap-1 transition duration-300"
                        :class="
                            $page.url === '/booking'
                                ? 'text-[#65AAC2]'
                                : 'text-[#869A69] hover:text-[#65AAC2]'
                        "
                    >
                        <!-- Icon Home (Solid) -->
                        <svg
                            class="h-6 w-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                            ></path>
                        </svg>
                        <span class="text-[8px] font-bold tracking-wide"
                            >Home</span
                        >
                    </Link>

                    <!-- 2. E-TICKET -->
                    <Link
                        href="/my-tickets"
                        class="flex w-12 flex-col items-center gap-1 transition duration-300"
                        :class="
                            $page.url.startsWith('/my-tickets')
                                ? 'text-[#65AAC2]'
                                : 'text-[#869A69] hover:text-[#65AAC2]'
                        "
                    >
                        <!-- Icon Ticket (Outline) -->
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                            ></path>
                        </svg>
                        <span class="text-[10px] font-bold tracking-wide"
                            >E-Ticket</span
                        >
                    </Link>

                    <!-- 3. PROFIL -->
                    <Link
                        href="/info"
                        class="flex w-12 flex-col items-center gap-1 transition duration-300"
                        :class="
                            $page.url.startsWith('/info')
                                ? 'text-[#65AAC2]'
                                : 'text-[#869A69] hover:text-[#65AAC2]'
                        "
                    >
                        <!-- Icon User (Outline) -->
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                        <span class="text-[8px] font-bold tracking-wide"
                            >Profil</span
                        >
                    </Link>
                </nav>
            </div>

            <!-- MODAL QR PREVIEW (SUDAH BENAR) -->
            <div
                v-if="showQrModal"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-[#4F5D46]/60 p-6 backdrop-blur-sm"
                @click="showQrModal = false"
            >
                <div
                    class="w-full max-w-xs rounded-[2rem] bg-white p-8 text-center shadow-2xl"
                    @click.stop
                >
                    <h3 class="mb-4 text-lg font-bold text-[#4F5D46]">
                        QR Check-in
                    </h3>
                    <div
                        class="mb-6 inline-block rounded-2xl border border-[#869A69]/30 p-4"
                    >
                        <img
                            :src="currentQrCode"
                            class="h-56 w-56 mix-blend-multiply"
                        />
                    </div>
                    <p
                        class="mb-4 rounded-lg bg-blue-50 py-2 font-mono text-xs font-bold text-[#65AAC2]"
                    >
                        {{ currentBookingCode }}
                    </p>
                    <button
                        @click="showQrModal = false"
                        class="w-full rounded-xl bg-[#65AAC2] py-3 text-sm font-bold text-white transition hover:opacity-90"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
