<script setup>
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Html5QrcodeScanner } from 'html5-qrcode';
import { onMounted, onUnmounted, ref } from 'vue';

// --- STATE ---
const activeTab = ref('scan'); // 'scan', 'history', 'profile'
const manualCode = ref('');
const scanner = ref(null);
const scanResult = ref(null);
const scanStatus = ref('');
const showModal = ref(false);
const historyList = ref([]);

// --- SCANNER LOGIC ---
const startScanner = () => {
    if (activeTab.value === 'scan' && !scanner.value) {
        setTimeout(() => {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
            };
            scanner.value = new Html5QrcodeScanner('reader', config, false);
            scanner.value.render(onScanSuccess, (err) => {});
        }, 100);
    }
};

const stopScanner = () => {
    if (scanner.value) {
        scanner.value.clear();
        scanner.value = null;
    }
};

const switchTab = (tab) => {
    activeTab.value = tab;
    if (tab === 'scan') startScanner();
    else stopScanner();
};

onMounted(() => startScanner());
onUnmounted(() => stopScanner());

const onScanSuccess = (decodedText) => {
    scanner.value.pause();
    validateTicket(decodedText);
};

const validateTicket = async (code) => {
    if (!code) return;
    try {
        const response = await axios.post(route('security.validate'), {
            booking_code: code,
        });
        scanResult.value = response.data.data;
        scanStatus.value = response.data.status;
        showModal.value = true;
    } catch (error) {
        scanStatus.value = 'error';
        scanResult.value = {
            message: error.response?.data?.message || 'Tiket Tidak Ditemukan',
        };
        showModal.value = true;
    }
};

const processCheckIn = () => {
    router.post(
        route('security.checkin'),
        { booking_code: scanResult.value.booking_code },
        {
            onSuccess: () => {
                historyList.value.unshift({
                    unit: scanResult.value.unit,
                    time: new Date().toLocaleTimeString('id-ID', {
                        hour: '2-digit',
                        minute: '2-digit',
                    }),
                    status: 'Check-In',
                });
                showModal.value = false;
                alert('Check-In Berhasil!');
                scanner.value.resume();
            },
        },
    );
};

const closeModal = () => {
    showModal.value = false;
    manualCode.value = '';
    if (scanner.value) scanner.value.resume();
};
</script>

<template>
    <Head title="Security Access" />

    <!-- LAYOUT LUAR (Simulasi Layar HP di Tengah) -->
    <div
        class="flex h-[100dvh] w-full justify-center bg-gray-200 font-sans antialiased"
    >
        <!-- MOBILE CONTAINER -->
        <div
            class="relative flex h-full w-full max-w-[480px] flex-col overflow-hidden bg-[#F2F4F7] shadow-2xl"
        >
            <!-- HEADER (Fixed Top) -->
            <header
                class="z-20 flex-none rounded-b-[2rem] bg-[#1C1C28] px-6 pt-10 pb-6 text-white shadow-lg"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.2em] text-[#D4F34A] uppercase"
                        >
                            Security Access
                        </p>
                        <h1
                            class="text-2xl font-black tracking-tight text-white"
                        >
                            Gate Control 🛡️
                        </h1>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#D4F34A] text-2xl shadow-[0_0_15px_#D4F34A66]"
                    >
                        👮‍♂️
                    </div>
                </div>
            </header>

            <!-- CONTENT AREA (Scrollable) -->
            <main class="no-scrollbar flex-1 overflow-y-auto px-6 pt-6 pb-32">
                <!-- TAB 1: SCANNER -->
                <div
                    v-if="activeTab === 'scan'"
                    class="animate-fade-in space-y-6"
                >
                    <!-- Scanner Box -->
                    <div
                        class="relative overflow-hidden rounded-[2.5rem] border-4 border-white bg-black shadow-xl"
                    >
                        <!-- Camera View -->
                        <div
                            class="relative flex min-h-[350px] items-center justify-center overflow-hidden bg-black"
                        >
                            <div
                                id="reader"
                                width="100%"
                                class="h-full w-full"
                            ></div>

                            <!-- Lime Overlay Frame -->
                            <div
                                class="pointer-events-none absolute inset-0 z-10 rounded-[2rem] border-[6px] border-[#D4F34A]/20"
                            ></div>

                            <!-- Target Box -->
                            <div
                                class="pointer-events-none absolute top-1/2 left-1/2 z-10 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-3xl border-[3px] border-[#D4F34A] shadow-[0_0_30px_#D4F34A44]"
                            >
                                <!-- Scanning Line Animation -->
                                <div
                                    class="animate-scan absolute top-0 left-0 h-1 w-full bg-[#D4F34A] shadow-[0_0_10px_#D4F34A]"
                                ></div>
                            </div>
                        </div>

                        <!-- Instruction -->
                        <div
                            class="absolute right-0 bottom-6 left-0 text-center"
                        >
                            <span
                                class="rounded-full bg-black/60 px-4 py-2 text-[10px] font-bold tracking-widest text-[#D4F34A] uppercase backdrop-blur-md"
                            >
                                Arahkan ke QR Code
                            </span>
                        </div>
                    </div>

                    <!-- Input Manual -->
                    <div
                        class="flex items-center gap-3 rounded-[1.5rem] bg-white p-3 shadow-sm"
                    >
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full bg-[#F2F4F7] text-slate-400"
                        >
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
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                ></path>
                            </svg>
                        </div>
                        <input
                            v-model="manualCode"
                            type="text"
                            placeholder="INPUT KODE..."
                            class="flex-1 border-none bg-transparent p-0 text-lg font-black tracking-widest text-[#1C1C28] uppercase placeholder-slate-300 focus:ring-0"
                            @keyup.enter="validateTicket(manualCode)"
                        />
                        <button
                            @click="validateTicket(manualCode)"
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1C1C28] text-[#D4F34A] shadow-lg transition active:scale-95"
                        >
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
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                                ></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- TAB 2: HISTORY -->
                <div v-if="activeTab === 'history'" class="animate-fade-in">
                    <div class="mb-6 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-[#1C1C28]">
                            History
                        </h3>
                        <span
                            class="rounded-lg bg-[#D4F34A] px-2 py-1 text-xs font-bold text-[#1C1C28]"
                            >Sesi Ini</span
                        >
                    </div>

                    <div
                        v-if="historyList.length === 0"
                        class="flex flex-col items-center justify-center py-20 opacity-40"
                    >
                        <div
                            class="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-slate-200"
                        >
                            <svg
                                class="h-10 w-10 text-slate-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-500">
                            Belum ada data scan.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(item, index) in historyList"
                            :key="index"
                            class="flex items-center justify-between rounded-[1.5rem] border border-white bg-white p-5 shadow-sm"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#D4F34A] text-lg font-black text-[#1C1C28]"
                                >
                                    IN
                                </div>
                                <div>
                                    <p class="text-lg font-bold text-[#1C1C28]">
                                        {{ item.unit }}
                                    </p>
                                    <div class="mt-1 flex items-center gap-1">
                                        <div
                                            class="h-2 w-2 rounded-full bg-green-500"
                                        ></div>
                                        <p
                                            class="text-[10px] font-bold tracking-wide text-slate-400 uppercase"
                                        >
                                            {{ item.status }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-[#1C1C28]">{{
                                item.time
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: PROFILE -->
                <div
                    v-if="activeTab === 'profile'"
                    class="animate-fade-in flex flex-col items-center pt-8"
                >
                    <div class="relative mb-6">
                        <div
                            class="absolute -inset-1 animate-pulse rounded-full bg-[#D4F34A] opacity-50 blur-lg"
                        ></div>
                        <div
                            class="relative flex h-32 w-32 items-center justify-center rounded-full border-4 border-white bg-[#1C1C28] text-6xl shadow-xl"
                        >
                            👮‍♂️
                        </div>
                    </div>

                    <h2 class="text-2xl font-black text-[#1C1C28]">
                        Petugas Jaga
                    </h2>
                    <p
                        class="mb-10 text-sm font-bold tracking-wide text-slate-400 uppercase"
                    >
                        Pos Utama • Shift 1
                    </p>

                    <div class="w-full space-y-4">
                        <button
                            class="group flex w-full items-center gap-4 rounded-3xl bg-white p-5 text-left shadow-sm transition hover:shadow-md"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#F2F4F7] text-[#1C1C28] transition group-hover:bg-[#1C1C28] group-hover:text-[#D4F34A]"
                            >
                                🔒
                            </div>
                            <span class="flex-1 font-bold text-[#1C1C28]"
                                >Ganti Password</span
                            >
                            <svg
                                class="h-5 w-5 text-slate-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                ></path>
                            </svg>
                        </button>

                        <button
                            @click="router.post('/logout')"
                            class="group mt-8 flex w-full items-center gap-4 rounded-3xl bg-[#FFEBEE] p-5 text-left shadow-sm transition hover:bg-[#FFCDD2]"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-[#EF583D] shadow-sm"
                            >
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
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    ></path>
                                </svg>
                            </div>
                            <span class="flex-1 font-bold text-[#EF583D]"
                                >Log Out</span
                            >
                        </button>
                    </div>
                </div>
            </main>

            <!-- BOTTOM NAVIGATION (Floating & Dark) -->
            <div class="absolute right-6 bottom-6 left-6 z-30">
                <nav
                    class="flex items-center justify-between rounded-[2.5rem] bg-[#1C1C28] px-8 py-4 shadow-2xl shadow-[#1C1C28]/40"
                >
                    <!-- SCAN -->
                    <button
                        @click="switchTab('scan')"
                        class="group relative flex flex-col items-center gap-1.5 transition-all duration-300"
                    >
                        <div
                            class="relative z-10 transition-transform duration-300"
                            :class="
                                activeTab === 'scan'
                                    ? '-translate-y-3 scale-110'
                                    : ''
                            "
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full transition-colors duration-300"
                                :class="
                                    activeTab === 'scan'
                                        ? 'bg-[#D4F34A] text-[#1C1C28] shadow-[0_0_15px_#D4F34A]'
                                        : 'text-slate-500 group-hover:text-white'
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <span
                            v-if="activeTab === 'scan'"
                            class="absolute -bottom-1 text-[9px] font-bold tracking-widest text-[#D4F34A]"
                            >SCAN</span
                        >
                    </button>

                    <!-- HISTORY -->
                    <button
                        @click="switchTab('history')"
                        class="group relative flex flex-col items-center gap-1.5 transition-all duration-300"
                    >
                        <div
                            class="relative z-10 transition-transform duration-300"
                            :class="
                                activeTab === 'history'
                                    ? '-translate-y-3 scale-110'
                                    : ''
                            "
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full transition-colors duration-300"
                                :class="
                                    activeTab === 'history'
                                        ? 'bg-[#D4F34A] text-[#1C1C28] shadow-[0_0_15px_#D4F34A]'
                                        : 'text-slate-500 group-hover:text-white'
                                "
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                        <span
                            v-if="activeTab === 'history'"
                            class="absolute -bottom-1 text-[9px] font-bold tracking-widest text-[#D4F34A]"
                            >LOGS</span
                        >
                    </button>

                    <!-- PROFILE -->
                    <button
                        @click="switchTab('profile')"
                        class="group relative flex flex-col items-center gap-1.5 transition-all duration-300"
                    >
                        <div
                            class="relative z-10 transition-transform duration-300"
                            :class="
                                activeTab === 'profile'
                                    ? '-translate-y-3 scale-110'
                                    : ''
                            "
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full transition-colors duration-300"
                                :class="
                                    activeTab === 'profile'
                                        ? 'bg-[#D4F34A] text-[#1C1C28] shadow-[0_0_15px_#D4F34A]'
                                        : 'text-slate-500 group-hover:text-white'
                                "
                            >
                                <svg
                                    class="h-5 w-5"
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
                            </div>
                        </div>
                        <span
                            v-if="activeTab === 'profile'"
                            class="absolute -bottom-1 text-[9px] font-bold tracking-widest text-[#D4F34A]"
                            >AKUN</span
                        >
                    </button>
                </nav>
            </div>

            <!-- MODAL RESULT -->
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-[#1C1C28]/90 p-6 backdrop-blur-md transition-opacity"
            >
                <div
                    class="animate-pop-up w-full max-w-sm overflow-hidden rounded-[2.5rem] bg-white shadow-2xl"
                >
                    <div
                        class="relative overflow-hidden p-8 text-center"
                        :class="{
                            'bg-[#D4F34A]': scanStatus === 'success',
                            'bg-[#FFF3E0]': scanStatus === 'warning',
                            'bg-[#FFEBEE]': scanStatus === 'error',
                        }"
                    >
                        <div
                            class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm"
                        >
                            <svg
                                v-if="scanStatus === 'success'"
                                class="h-10 w-10 text-[#1C1C28]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            <svg
                                v-else
                                class="h-10 w-10 text-[#EF583D]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </div>
                        <h2
                            class="text-2xl font-black tracking-tight"
                            :class="{
                                'text-[#1C1C28]': scanStatus === 'success',
                                'text-[#EF583D]': scanStatus !== 'success',
                            }"
                        >
                            {{
                                scanStatus === 'success'
                                    ? 'TIKET VALID'
                                    : scanStatus === 'warning'
                                      ? 'PERINGATAN'
                                      : 'DITOLAK'
                            }}
                        </h2>
                        <p
                            class="mt-1 text-xs font-bold tracking-wide uppercase opacity-70"
                            :class="
                                scanStatus === 'success'
                                    ? 'text-[#1C1C28]'
                                    : 'text-slate-600'
                            "
                        >
                            {{ scanResult?.message }}
                        </p>
                    </div>

                    <div v-if="scanStatus !== 'error'" class="space-y-4 p-6">
                        <div
                            class="flex items-center justify-between rounded-2xl border border-slate-100 bg-[#F5F7F9] p-4"
                        >
                            <span
                                class="text-xs font-bold tracking-widest text-slate-400 uppercase"
                                >Unit</span
                            >
                            <span class="text-xl font-black text-[#1C1C28]">{{
                                scanResult.unit
                            }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-2xl border border-slate-100 bg-[#F5F7F9] p-4"
                        >
                            <span
                                class="text-xs font-bold tracking-widest text-slate-400 uppercase"
                                >Jam</span
                            >
                            <span class="text-xl font-black text-[#1C1C28]">{{
                                scanResult.time
                            }}</span>
                        </div>

                        <!-- TOMBOL CHECK IN BESAR -->
                        <button
                            v-if="scanStatus === 'success'"
                            @click="processCheckIn"
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-[1.5rem] bg-[#1C1C28] py-4 font-bold text-[#D4F34A] shadow-xl shadow-[#1C1C28]/30 transition hover:bg-black active:scale-95"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            CHECK-IN SEKARANG
                        </button>
                    </div>

                    <div class="p-4 pt-0">
                        <button
                            @click="closeModal"
                            class="w-full py-3 text-xs font-bold text-slate-400 uppercase transition hover:text-[#1C1C28]"
                        >
                            Tutup / Scan Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Override Style Scanner */
#reader {
    border: none !important;
}
#reader video {
    object-fit: cover;
    border-radius: 1.5rem;
    height: 100% !important;
}
@keyframes scan {
    0% {
        top: 10%;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        top: 90%;
        opacity: 0;
    }
}
.animate-scan {
    animation: scan 2s infinite ease-in-out;
}
@keyframes popUp {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-pop-up {
    animation: popUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}

/* Hide Scrollbar */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
