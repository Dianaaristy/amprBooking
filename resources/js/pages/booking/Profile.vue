<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    unit: Object,
    userUnit: String,
});

// --- STATE PIN MODAL ---
const showPinModal = ref(false);
const form = useForm({
    current_pin: '',
    new_pin: '',
    new_pin_confirmation: '',
});

const submitPin = () => {
    form.put(route('resident.update-pin'), {
        onSuccess: () => {
            showPinModal.value = false;
            form.reset();
            alert('PIN Berhasil Diubah!'); // Simple alert feedback
        },
        onError: () => {
            form.reset('current_pin', 'new_pin', 'new_pin_confirmation');
        },
    });
};

// --- LOGOUT ---
const logout = () => {
    router.post(
        route('logout'),
        {},
        {
            onFinish: () => alert('Anda berhasil keluar.'),
        },
    );
};

// --- RULES DATA ---
const rules = [
    {
        title: 'Kuota Mingguan',
        desc: 'Setiap unit memiliki jatah maksimal 2 JAM per minggu (Senin - Minggu). Kuota reset otomatis setiap hari Senin dini hari.',
        icon: 'clock',
    },
    {
        title: 'Batas Keterlambatan',
        desc: 'Wajib Check-in (Scan QR) maksimal 15 menit setelah jam mulai. Jika lewat, tiket hangus (No Show).',
        icon: 'exclamation',
    },
    {
        title: 'Sanksi Banned',
        desc: 'Jika terkena status "No Show" (tidak datang/telat scan), Unit akan otomatis di-BANNED (tidak bisa booking) selama 7 hari.',
        icon: 'ban',
    },
    {
        title: 'Kebersihan & Sepatu',
        desc: 'Wajib menggunakan sepatu olahraga (Non-marking). Dilarang makan/merokok di area lapangan.',
        icon: 'trash',
    },
];
</script>

<template>
    <Head title="Info & Profil" />

    <div
        class="flex h-[100dvh] justify-center overflow-hidden bg-[#E7E5D7] font-sans text-slate-800 antialiased"
    >
        <div
            class="relative flex h-full w-full max-w-[480px] flex-col overflow-hidden border-x border-[#869A69]/20 bg-white shadow-2xl"
        >
            <!-- HEADER -->
            <div class="z-10 flex-none bg-white px-6 pt-10 pb-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-black text-[#65AAC2]">
                        Info & Profil
                    </h1>

                    <!-- TOMBOL LOGOUT -->
                    <!-- Pastikan type="button" agar tidak submit form sembarangan -->
                    <button
                        type="button"
                        @click="logout"
                        class="rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-500 transition hover:bg-red-100"
                    >
                        Logout
                    </button>
                </div>
            </div>

            <!-- SCROLLABLE CONTENT -->
            <main
                class="no-scrollbar flex-1 overflow-y-auto bg-[#FAFAFA] px-6 pt-6 pb-24"
            >
                <!-- CARD PROFIL UNIT -->
                <div
                    class="relative mb-8 overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-[#65AAC2] to-[#528ea3] p-6 text-white shadow-lg"
                >
                    <div class="relative z-10 flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20 text-2xl backdrop-blur-md"
                        >
                            🏢
                        </div>
                        <div>
                            <p
                                class="text-xs font-medium tracking-widest text-blue-100 uppercase"
                            >
                                Nomor Unit
                            </p>
                            <h2 class="text-3xl font-black tracking-tight">
                                {{ unit.unit_number }}
                            </h2>
                            <p class="mt-1 text-sm font-medium text-blue-50">
                                {{ unit.owner_name || 'Penghuni' }}
                            </p>
                        </div>
                    </div>
                    <!-- Decorative Circles -->
                    <div
                        class="absolute -top-4 -right-4 h-24 w-24 rounded-full bg-white/10"
                    ></div>
                    <div
                        class="absolute -right-10 bottom-0 h-32 w-32 rounded-full border-[20px] border-white/5"
                    ></div>
                </div>

                <!-- MENU AKSI -->
                <div class="mb-8 space-y-3">
                    <h3
                        class="mb-3 ml-1 text-xs font-bold tracking-widest text-gray-400 uppercase"
                    >
                        Pengaturan
                    </h3>

                    <button
                        @click="showPinModal = true"
                        class="flex w-full items-center justify-between rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:bg-gray-50"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="rounded-lg bg-orange-100 p-2 text-orange-600"
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
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    ></path>
                                </svg>
                            </div>
                            <span class="font-bold text-gray-700"
                                >Ganti PIN Akses</span
                            >
                        </div>
                        <svg
                            class="h-5 w-5 text-gray-300"
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
                </div>

                <!-- ATURAN PENGGUNAAN -->
                <div class="space-y-4">
                    <h3
                        class="mb-2 ml-1 text-xs font-bold tracking-widest text-gray-400 uppercase"
                    >
                        Tata Tertib
                    </h3>

                    <div
                        v-for="(rule, idx) in rules"
                        :key="idx"
                        class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Icons -->
                            <div class="mt-0.5 text-[#869A69]">
                                <svg
                                    v-if="rule.icon === 'clock'"
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
                                <svg
                                    v-if="rule.icon === 'exclamation'"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                    ></path>
                                </svg>
                                <svg
                                    v-if="rule.icon === 'ban'"
                                    class="h-5 w-5 text-red-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                    ></path>
                                </svg>
                                <svg
                                    v-if="rule.icon === 'trash'"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    ></path>
                                </svg>
                            </div>
                            <div>
                                <h4
                                    class="mb-1 text-sm font-bold text-gray-800"
                                >
                                    {{ rule.title }}
                                </h4>
                                <p
                                    class="text-xs leading-relaxed text-gray-500"
                                >
                                    {{ rule.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">
                        AMPR Management System v1.0
                    </p>
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

            <!-- MODAL GANTI PIN -->
            <div
                v-if="showPinModal"
                class="fixed inset-0 z-[60] flex items-end justify-center p-0 sm:items-center sm:p-4"
            >
                <div
                    @click="showPinModal = false"
                    class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                ></div>
                <div
                    class="animate-slide-up relative z-10 max-h-[90vh] w-full max-w-[480px] overflow-y-auto rounded-t-[2.5rem] bg-white p-8 shadow-2xl sm:rounded-[2.5rem]"
                >
                    <div
                        class="mx-auto mb-8 h-1.5 w-12 rounded-full bg-[#E7E5D7]"
                    ></div>

                    <h2 class="mb-6 text-xl font-black text-[#65AAC2]">
                        Ganti PIN Akses
                    </h2>

                    <form @submit.prevent="submitPin" class="space-y-4">
                        <div>
                            <label
                                class="mb-2 ml-1 block text-xs font-bold text-[#869A69] uppercase"
                                >PIN Lama</label
                            >
                            <input
                                v-model="form.current_pin"
                                type="password"
                                class="w-full rounded-2xl border-none bg-[#F3F4F6] px-5 py-3.5 font-bold text-gray-700 focus:ring-2 focus:ring-[#65AAC2]"
                                placeholder="••••"
                            />
                            <p
                                v-if="form.errors.current_pin"
                                class="mt-1 ml-1 text-xs text-red-500"
                            >
                                {{ form.errors.current_pin }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 ml-1 block text-xs font-bold text-[#869A69] uppercase"
                                >PIN Baru (Min 4 digit)</label
                            >
                            <input
                                v-model="form.new_pin"
                                type="password"
                                class="w-full rounded-2xl border-none bg-[#F3F4F6] px-5 py-3.5 font-bold text-gray-700 focus:ring-2 focus:ring-[#65AAC2]"
                                placeholder="••••"
                            />
                            <p
                                v-if="form.errors.new_pin"
                                class="mt-1 ml-1 text-xs text-red-500"
                            >
                                {{ form.errors.new_pin }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-2 ml-1 block text-xs font-bold text-[#869A69] uppercase"
                                >Konfirmasi PIN Baru</label
                            >
                            <input
                                v-model="form.new_pin_confirmation"
                                type="password"
                                class="w-full rounded-2xl border-none bg-[#F3F4F6] px-5 py-3.5 font-bold text-gray-700 focus:ring-2 focus:ring-[#65AAC2]"
                                placeholder="••••"
                            />
                        </div>

                        <button
                            :disabled="form.processing"
                            class="mt-4 w-full rounded-2xl bg-[#65AAC2] py-4 font-bold text-white shadow-xl shadow-[#65AAC2]/30 transition hover:bg-[#528ea3] disabled:opacity-70"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan PIN Baru'
                            }}
                        </button>
                    </form>
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
@keyframes slide-up {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}
.animate-slide-up {
    animation: slide-up 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
