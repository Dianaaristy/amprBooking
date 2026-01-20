<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// --- 1. LOGIC (TETAP SAMA 100%) ---
const props = defineProps({
    dates: { type: Array, default: () => [] },
    bookedSlots: { type: Array, default: () => [] },
    units: { type: Array, default: () => [] },
    fullyBookedDates: { type: Array, default: () => [] },
    userUnit: { type: String, required: true },
});

const todayStr = new Date().toISOString().split('T')[0];
const selectedDate = ref(
    props.dates.length > 0 ? props.dates[0].full_date : todayStr,
);
const showModal = ref(false);
const selectedSlot = ref(null);

const form = useForm({
    player_names: '',
    date: '',
    hour: '',
    agree_terms: false,
});

const morningSlots = [6, 7, 8, 9, 10, 11];
const eveningSlots = [15, 16, 17, 18, 19, 20, 21];

const getStatus = (hour) => {
    const slots = props.bookedSlots || [];
    const isBooked = slots.some(
        (slot) => slot.date === selectedDate.value && slot.hour === hour,
    );
    if (isBooked) return 'booked';
    const now = new Date();
    const localToday = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .split('T')[0];
    const currentHour = now.getHours();
    if (selectedDate.value < localToday) return 'past';
    if (selectedDate.value === localToday && hour <= currentHour) return 'past';
    return 'available';
};

const openBooking = (hour) => {
    if (getStatus(hour) !== 'available') return;
    const start = String(hour).padStart(2, '0') + ':00';
    const end = String(hour + 1).padStart(2, '0') + ':00';
    selectedSlot.value = { hour, label: `${start} - ${end}` };
    // Reset Form (Lebih Simpel)
    form.date = selectedDate.value;
    form.hour = hour;
    form.player_names = ''; // Reset nama
    form.agree_terms = false;
    form.clearErrors();

    showModal.value = true;
};

const submitBooking = () => {
    form.post(route('booking.store'), {
        preserveScroll: true, // Supaya scroll ga loncat
        onSuccess: () => (showModal.value = false),
        onError: (errors) => {
            // Opsional: Debugging di console
            console.log('Error Booking:', errors);
        },
    });
};

const formatDateDisplay = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
};
</script>

<template>
    <Head title="Booking Lapangan" />

    <!-- LAYOUT UTAMA: Background Beige/Cement (#E7E5D7) -->
    <div
        class="flex h-[100dvh] justify-center overflow-hidden bg-[#E7E5D7] font-sans text-slate-800 antialiased"
    >
        <!-- MOBILE CONTAINER -->
        <div
            class="relative flex h-full w-full max-w-[480px] flex-col overflow-hidden border-x border-[#869A69]/20 bg-white shadow-2xl"
        >
            <!-- BAGIAN 1: HEADER (Sticky Top) -->
            <div class="flex-none bg-white px-6 pt-10 pb-4">
                <!-- Top Bar -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                        >
                            LOKASI
                        </p>
                        <div class="flex items-center gap-1.5 text-slate-800">
                            <svg
                                class="h-4 w-4 text-[#65AAC2]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg>
                            <h1 class="text-lg font-bold tracking-tight">
                                Mediterania Palace
                            </h1>
                        </div>
                    </div>
                    <!-- Notif Icon -->
                    <div
                        class="relative cursor-pointer rounded-full border border-[#E7E5D7] bg-[#F8F9FA] p-2.5 transition hover:bg-[#E7E5D7]/50"
                    >
                        <svg
                            class="h-5 w-5 text-[#869A69]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            ></path>
                        </svg>
                        <div
                            class="absolute top-2.5 right-3 h-1.5 w-1.5 rounded-full bg-[#65AAC2]"
                        ></div>
                    </div>
                </div>

                <!-- Hero Card: Court Blue (#65AAC2) -->
                <div
                    class="relative overflow-hidden rounded-[1.5rem] bg-[#65AAC2] p-6 text-white shadow-xl shadow-[#65AAC2]/30"
                >
                    <div class="relative z-10">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2
                                    class="mb-1 font-serif text-2xl leading-tight font-bold tracking-wide"
                                >
                                    Tennis Court 🎾
                                </h2>
                                <p
                                    class="text-xs font-medium tracking-wider text-[#E7E5D7] uppercase"
                                >
                                    Booking Anytime
                                </p>
                            </div>
                            <!-- Badge -->
                            <div
                                class="rounded-lg border border-white/10 bg-white/20 px-3 py-1.5 text-center backdrop-blur-md"
                            >
                                <span
                                    class="block text-[9px] tracking-widest text-[#E7E5D7] uppercase"
                                    >KUOTA</span
                                >
                                <span class="text-sm font-bold">2 Jam</span>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-3">
                            <div
                                class="rounded-full bg-[#ADBA5E] p-2 text-white shadow-sm"
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
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                            <div
                                class="text-sm font-bold tracking-wide text-white"
                            >
                                {{
                                    new Date().toLocaleDateString('id-ID', {
                                        month: 'long',
                                        year: 'numeric',
                                    })
                                }}
                            </div>
                        </div>
                    </div>

                    <!-- Decorative Lines (Abstract Tennis Court) -->
                    <div
                        class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full border-[20px] border-white/20"
                    ></div>
                    <div
                        class="absolute top-4 right-10 h-[2px] w-20 rotate-45 bg-white/10"
                    ></div>
                </div>
            </div>

            <!-- BAGIAN TANGGAL (SCROLLABLE) -->
            <main class="no-scrollbar flex-1 overflow-y-auto px-6 pt-2 pb-24">
                <div class="mb-8">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-[#869A69]">
                            Pilih Tanggal
                        </h3>
                        <span
                            class="rounded-lg bg-[#E8EAF6] px-2 py-1 text-xs font-medium text-[#869A69]"
                        >
                            {{
                                new Date(selectedDate).toLocaleDateString(
                                    'id-ID',
                                    { month: 'long' },
                                )
                            }}
                        </span>
                    </div>

                    <!-- Scroll Horizontal Tanggal -->
                    <div
                        class="no-scrollbar flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2"
                    >
                        <button
                            v-for="day in dates"
                            :key="day.full_date"
                            @click="selectedDate = day.full_date"
                            class="relative flex h-[75px] min-w-[60px] snap-center flex-col items-center justify-center rounded-[1.5rem] border transition-all duration-300"
                            :class="[
                                selectedDate === day.full_date
                                    ? 'scale-105 bg-[#65AAC2] text-white shadow-lg shadow-[#65AAC2]/30'
                                    : 'border-[#869A69] bg-white text-[#869A69] hover:border-[#65AAC2]',
                                fullyBookedDates.includes(day.full_date) &&
                                selectedDate !== day.full_date
                                    ? 'opacity-50 grayscale'
                                    : '',
                            ]"
                        >
                            <span
                                class="mb-1 text-[10px] font-bold tracking-widest uppercase"
                            >
                                {{ day.day_name }}
                            </span>
                            <span class="text-lg font-black">
                                {{ day.date_num }}
                            </span>

                            <div
                                v-if="fullyBookedDates.includes(day.full_date)"
                                class="absolute top-2 right-2 h-1.5 w-1.5 rounded-full bg-[#EF583D]"
                            ></div>
                        </button>
                    </div>
                </div>

                <!-- Slot List -->
                <div class="space-y-6">
                    <!-- Pagi -->
                    <div>
                        <div class="mb-4 flex items-center gap-2">
                            <!-- Dot Indikator Pagi: Menggunakan Court Blue -->
                            <span
                                class="h-2 w-2 rounded-full bg-[#65AAC2]"
                            ></span>
                            <!-- Label: Menggunakan Court Green -->
                            <span
                                class="text-xs font-bold tracking-widest text-[#869A69] uppercase"
                                >Pagi (06:00 - 11:00)</span
                            >
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <div
                                v-for="hour in morningSlots"
                                :key="hour"
                                @click="openBooking(hour)"
                                class="group relative flex items-center justify-between rounded-[1.2rem] bg-white p-4 transition-all duration-300"
                                :class="[
                                    getStatus(hour) === 'available'
                                        ? 'cursor-pointer hover:shadow-lg hover:shadow-[#65AAC2]/10'
                                        : 'cursor-not-allowed bg-[#F9FAFB] opacity-50',
                                ]"
                            >
                                <div class="flex items-center gap-4">
                                    <!-- Jam Icon: Background Court Blue tipis, Teks Court Blue -->
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-xs font-bold"
                                        :class="
                                            getStatus(hour) === 'available'
                                                ? 'bg-[#65AAC2]/10 text-[#65AAC2]'
                                                : 'bg-gray-100 text-gray-400'
                                        "
                                    >
                                        {{ String(hour).padStart(2, '0') }}
                                    </div>
                                    <div>
                                        <!-- Jam Text: Abu tua netral -->
                                        <p
                                            class="text-sm font-bold text-[#374151]"
                                        >
                                            {{
                                                String(hour).padStart(2, '0')
                                            }}:00 -
                                            {{
                                                String(hour + 1).padStart(
                                                    2,
                                                    '0',
                                                )
                                            }}:00
                                        </p>
                                        <!-- Status Text: Tennis Ball Green untuk Available -->
                                        <p
                                            class="mt-0.5 text-[10px] font-bold uppercase"
                                            :class="
                                                getStatus(hour) === 'available'
                                                    ? 'text-[#ADBA5E]'
                                                    : 'text-gray-400'
                                            "
                                        >
                                            {{
                                                getStatus(hour) === 'available'
                                                    ? 'Available'
                                                    : 'Unavailable'
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <!-- Add Button: Court Blue -->
                                <button
                                    class="flex h-8 w-8 items-center justify-center rounded-full transition-all duration-300"
                                    :class="
                                        getStatus(hour) === 'available'
                                            ? 'bg-[#65AAC2] text-white shadow-md shadow-[#65AAC2]/30 group-hover:scale-110'
                                            : 'bg-transparent text-gray-300'
                                    "
                                >
                                    <span
                                        v-if="getStatus(hour) === 'available'"
                                        class="mb-0.5 text-lg font-bold"
                                        >+</span
                                    >
                                    <svg
                                        v-else
                                        class="h-4 w-4"
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
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sore -->
                    <div>
                        <div class="mb-4 flex items-center gap-2">
                            <!-- Dot Indikator Sore: Menggunakan Tennis Ball Green untuk pembeda -->
                            <span
                                class="h-2 w-2 rounded-full bg-[#ADBA5E]"
                            ></span>
                            <span
                                class="text-xs font-bold tracking-widest text-[#869A69] uppercase"
                                >Sore (15:00 - 21:00)</span
                            >
                        </div>
                        <div class="grid grid-cols-1 gap-3">
                            <div
                                v-for="hour in eveningSlots"
                                :key="hour"
                                @click="openBooking(hour)"
                                class="group relative flex items-center justify-between rounded-[1.2rem] bg-white p-4 transition-all duration-300"
                                :class="[
                                    getStatus(hour) === 'available'
                                        ? 'cursor-pointer hover:shadow-lg hover:shadow-[#65AAC2]/10'
                                        : 'cursor-not-allowed bg-[#F9FAFB] opacity-50',
                                ]"
                            >
                                <div class="flex items-center gap-4">
                                    <!-- Jam Icon: Tennis Ball Green tipis -->
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full text-xs font-bold"
                                        :class="
                                            getStatus(hour) === 'available'
                                                ? 'bg-[#ADBA5E]/10 text-[#ADBA5E]'
                                                : 'bg-gray-100 text-gray-400'
                                        "
                                    >
                                        {{ String(hour).padStart(2, '0') }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-sm font-bold text-[#374151]"
                                        >
                                            {{
                                                String(hour).padStart(2, '0')
                                            }}:00 -
                                            {{
                                                String(hour + 1).padStart(
                                                    2,
                                                    '0',
                                                )
                                            }}:00
                                        </p>
                                        <p
                                            class="mt-0.5 text-[10px] font-bold uppercase"
                                            :class="
                                                getStatus(hour) === 'available'
                                                    ? 'text-[#ADBA5E]'
                                                    : 'text-gray-400'
                                            "
                                        >
                                            {{
                                                getStatus(hour) === 'available'
                                                    ? 'Available'
                                                    : 'Unavailable'
                                            }}
                                        </p>
                                    </div>
                                </div>
                                <button
                                    class="flex h-8 w-8 items-center justify-center rounded-full transition-all duration-300"
                                    :class="
                                        getStatus(hour) === 'available'
                                            ? 'bg-[#ADBA5E] text-white shadow-md shadow-[#ADBA5E]/30 group-hover:scale-110'
                                            : 'bg-transparent text-gray-300'
                                    "
                                >
                                    <span
                                        v-if="getStatus(hour) === 'available'"
                                        class="mb-0.5 text-lg font-bold"
                                        >+</span
                                    >
                                    <svg
                                        v-else
                                        class="h-4 w-4"
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
                                </button>
                            </div>
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
            <!-- MODAL KONFIRMASI -->
            <div
                v-if="showModal"
                class="fixed inset-0 z-[60] flex items-end justify-center p-0 sm:items-center sm:p-4"
            >
                <div
                    @click="showModal = false"
                    class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                ></div>
                <div
                    class="animate-slide-up relative z-10 max-h-[90vh] w-full max-w-[480px] overflow-y-auto rounded-t-[2.5rem] bg-white p-8 shadow-2xl sm:rounded-[2.5rem]"
                >
                    <div
                        class="mx-auto mb-8 h-1.5 w-12 rounded-full bg-[#E7E5D7]"
                    ></div>

                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-black text-[#65AAC2]">
                                Booking
                            </h2>
                            <p class="mt-1 text-sm font-medium text-[#869A69]">
                                {{ formatDateDisplay(selectedDate) }} •
                                {{ selectedSlot?.label }}
                            </p>
                        </div>
                    </div>

                    <!-- ERROR FIELD -->
                    <div
                        v-if="Object.keys(form.errors).length > 0"
                        class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Icon Warning -->
                            <svg
                                class="mt-0.5 h-5 w-5 text-red-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-red-800">
                                    Gagal Booking
                                </h4>
                                <ul
                                    class="mt-1 list-disc pl-4 text-xs font-medium text-red-600"
                                >
                                    <li
                                        v-for="(error, key) in form.errors"
                                        :key="key"
                                    >
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitBooking" class="space-y-5">
                        <!--  INFO UNIT -->
                        <div
                            class="rounded-2xl border border-[#869A69]/20 bg-[#E7E5D7]/50 p-4"
                        >
                            <div
                                class="text-[10px] font-bold tracking-widest text-[#869A69] uppercase"
                            >
                                Booking Atas Nama Unit
                            </div>
                            <div class="mt-1 text-xl font-black text-[#374151]">
                                {{ userUnit }}
                            </div>
                        </div>
                        <!-- Input Nama -->
                        <div>
                            <label
                                class="mb-2 ml-1 block text-xs font-bold tracking-wider text-[#869A69] uppercase"
                                >Nama Pemain</label
                            >
                            <input
                                v-model="form.player_names"
                                type="text"
                                placeholder="Budi & Keluarga"
                                class="w-full rounded-2xl border-none bg-[#E7E5D7] py-4 pl-5 font-medium text-[#374151] placeholder-[#869A69]/50 transition focus:bg-white focus:ring-2 focus:ring-[#65AAC2]"
                            />
                        </div>

                        <!-- Terms -->
                        <div
                            class="flex items-start gap-3 rounded-2xl border border-[#E7E5D7] bg-white p-4"
                            :class="{
                                'border-red-400': form.errors.agree_terms,
                            }"
                        >
                            <input
                                id="terms"
                                type="checkbox"
                                v-model="form.agree_terms"
                                class="mt-1 h-5 w-5 rounded-md border-[#869A69] text-[#65AAC2] focus:ring-[#65AAC2]"
                            />
                            <label
                                for="terms"
                                class="text-xs leading-relaxed font-medium text-[#869A69]"
                            >
                                Saya setuju dengan tata tertib. Keterlambatan
                                >15 menit sanksi
                                <span class="font-bold text-red-400"
                                    >banned 1 minggu.</span
                                >
                            </label>
                        </div>

                        <!-- Button: Court Blue Solid untuk kesan profesional -->
                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-[#65AAC2] py-4 font-bold text-white shadow-xl shadow-[#65AAC2]/30 transition hover:opacity-90 disabled:opacity-70"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>Booking Sekarang</span>
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
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                                ></path>
                            </svg>
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
