<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    booking: Object,
    qrCode: String,
});

const showQrModal = ref(false);

const formattedDate = computed(() => {
    return new Date(props.booking.start_time).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
});

const formattedTime = computed(() => {
    const s = new Date(props.booking.start_time);
    const e = new Date(props.booking.end_time);
    const opt = { hour: '2-digit', minute: '2-digit', hour12: false };
    return `${s.toLocaleTimeString('id-ID', opt)} - ${e.toLocaleTimeString('id-ID', opt)}`;
});
</script>

<template>
    <Head title="Booking Berhasil" />

    <!-- LAYOUT UTAMA -->
    <div
        class="flex min-h-[100dvh] justify-center bg-[#E7E5D7] font-sans text-[#4F5D46] antialiased"
    >
        <!-- MOBILE CONTAINER -->
        <div
            class="relative flex min-h-[100dvh] w-full max-w-[480px] flex-col items-center justify-between border-x border-white/50 bg-white/60 p-6 shadow-2xl backdrop-blur-xl"
        >
            <!-- ===== BAGIAN ATAS ===== -->
            <div class="flex w-full flex-col items-center">
                <!-- Success Icon -->
                <div
                    class="relative mb-6 flex h-24 w-24 items-center justify-center"
                >
                    <div
                        class="absolute h-full w-full animate-ping rounded-full bg-[#65AAC2]/30"
                    ></div>
                    <div
                        class="relative flex h-20 w-20 items-center justify-center rounded-full bg-[#65AAC2] text-white shadow-lg shadow-[#65AAC2]/40"
                    >
                        <svg
                            class="h-10 w-10"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>
                </div>

                <h1 class="mb-2 text-2xl font-black text-[#4F5D46]">
                    Booking Berhasil!
                </h1>
                <p class="mb-8 text-center text-sm font-medium text-[#869A69]">
                    Jadwal lapangan tenis telah diamankan.<br />
                    Silakan simpan tiket Anda.
                </p>

                <!-- Ticket Card -->
                <div
                    class="w-full overflow-hidden rounded-[2rem] border border-[#869A69]/30 bg-white/80 shadow-xl backdrop-blur-md"
                >
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between border-b border-[#869A69]/20 p-6"
                    >
                        <div>
                            <div
                                class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                            >
                                Kode Booking
                            </div>
                            <div
                                class="mt-1 font-mono text-2xl font-black tracking-tight text-[#65AAC2]"
                            >
                                {{
                                    booking.booking_code
                                        .substring(0, 8)
                                        .toUpperCase()
                                }}
                            </div>
                        </div>

                        <button
                            @click="showQrModal = true"
                            class="rounded-xl border border-[#869A69]/30 bg-white p-1.5 shadow-sm transition hover:scale-105 active:scale-95"
                        >
                            <img :src="qrCode" class="h-12 w-12" alt="QR" />
                        </button>
                    </div>

                    <!-- Detail -->
                    <div class="grid grid-cols-2 gap-x-4 gap-y-6 p-6 pb-8">
                        <!-- Tanggal -->
                        <div class="col-span-2">
                            <div class="mb-1 flex items-center gap-2">
                                <svg
                                    class="h-4 w-4 text-[#65AAC2]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <span
                                    class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                                >
                                    Tanggal Main
                                </span>
                            </div>
                            <div
                                class="pl-6 text-base font-bold text-[#4F5D46]"
                            >
                                {{ formattedDate }}
                            </div>
                        </div>

                        <!-- Jam -->
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <svg
                                    class="h-4 w-4 text-[#65AAC2]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span
                                    class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                                >
                                    Jam
                                </span>
                            </div>
                            <div class="pl-6 text-sm font-bold text-[#4F5D46]">
                                {{ formattedTime }}
                            </div>
                        </div>

                        <!-- Unit -->
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <svg
                                    class="h-4 w-4 text-[#65AAC2]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                <span
                                    class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                                >
                                    Unit
                                </span>
                            </div>
                            <div class="pl-6 text-sm font-bold text-[#4F5D46]">
                                {{ booking.unit?.unit_number }}
                            </div>
                        </div>

                        <!-- Pemain -->
                        <div
                            class="col-span-2 mt-2 border-t border-[#869A69]/20 pt-4"
                        >
                            <div class="mb-1 flex items-center gap-2">
                                <svg
                                    class="h-4 w-4 text-[#65AAC2]"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                <span
                                    class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                                >
                                    Pemain
                                </span>
                            </div>
                            <div
                                class="pl-6 text-sm font-bold break-words text-[#4F5D46]"
                            >
                                {{
                                    Array.isArray(booking.player_names)
                                        ? booking.player_names[0]
                                        : booking.player_names
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== BAGIAN BAWAH (SELALU TERLIHAT) ===== -->
            <div class="w-full">
                <a
                    :href="`/ticket/${booking.booking_code}/pdf`"
                    target="_blank"
                    class="mt-3 flex w-full items-center justify-center gap-2 rounded-2xl bg-[#65AAC2] py-4 font-bold text-white shadow-xl shadow-[#65AAC2]/30 transition hover:opacity-90 active:scale-95"
                >
                    Download E-Ticket
                </a>

                <Link
                    href="/"
                    class="mt-4 block text-center text-sm font-bold text-[#869A69] transition hover:text-[#4F5D46]"
                >
                    Booking Slot Lain
                </Link>
            </div>
        </div>

        <!-- MODAL QR -->
        <div
            v-if="showQrModal"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-[#4F5D46]/60 p-6 backdrop-blur-sm"
            @click="showQrModal = false"
        >
            <div
                class="w-full max-w-xs rounded-[2rem] bg-white p-8 text-center shadow-2xl"
                @click.stop
            >
                <div
                    class="mb-6 inline-block rounded-2xl border border-[#869A69]/30 p-4"
                >
                    <img :src="qrCode" class="h-56 w-56 mix-blend-multiply" />
                </div>
                <h3 class="mb-2 text-lg font-bold text-[#4F5D46]">Scan Me</h3>
                <p class="text-sm font-medium text-[#869A69]">
                    Tunjukkan QR Code ini kepada petugas.
                </p>
                <button
                    @click="showQrModal = false"
                    class="mt-6 w-full rounded-xl bg-[#65AAC2] py-3 text-sm font-bold text-white transition hover:opacity-90"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</template>
