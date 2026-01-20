<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    days: Array,
    bookings: Array,
    stats: Object,
    units: Array,
});

const showBookingModal = ref(false);

const formBooking = useForm({
    unit_id: '',
    date: '',
    hour: '',
    displayDate: '',
    displayTime: '',
});

// --- LOGIKA CEK WAKTU LAMPAU ---
const isPast = (dateStr, hour) => {
    const now = new Date();
    // Buat object date untuk slot ini (T format ISO)
    const slotDate = new Date(
        `${dateStr}T${String(hour).padStart(2, '0')}:00:00`,
    );

    // Bandingkan: Jika slotDate < sekarang, berarti masa lalu
    return slotDate < now;
};

const getBooking = (date, hour) => {
    return props.bookings.find((b) => b.date === date && b.hour === hour);
};

const getSlotStyle = (booking) => {
    if (!booking) return '';
    switch (booking.status) {
        case 'checked_in':
            return 'bg-emerald-100 border-emerald-200 text-emerald-700 hover:bg-emerald-200';
        case 'no_show':
            return 'bg-rose-100 border-rose-200 text-rose-700 hover:bg-rose-200';
        case 'maintenance':
            return 'bg-slate-200 border-slate-300 text-slate-600 cursor-not-allowed hover:bg-slate-300';
        case 'booked':
        default:
            return 'bg-blue-100 border-blue-200 text-blue-700 hover:bg-blue-200';
    }
};

const openBookingModal = (date, hour, formattedDate) => {
    formBooking.unit_id = '';
    formBooking.date = date;
    formBooking.hour = hour;
    formBooking.displayDate = formattedDate;
    formBooking.displayTime = `${String(hour).padStart(2, '0')}:00 - ${String(hour + 1).padStart(2, '0')}:00`;
    formBooking.clearErrors(); // Bersihkan error lama
    showBookingModal.value = true;
};

const submitBooking = () => {
    formBooking.post(route('admin.dashboard.store'), {
        onSuccess: () => {
            showBookingModal.value = false;
            formBooking.reset();
        },
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>Dashboard</template>

        <!-- STATS GRID -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div
                class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#1A5F7A] to-[#154c61] p-6 text-white shadow-xl shadow-[#1A5F7A]/20"
            >
                <p
                    class="mb-1 text-xs font-bold tracking-widest uppercase opacity-70"
                >
                    Total Unit
                </p>
                <h3 class="text-4xl font-black">{{ stats.total_units }}</h3>
            </div>
            <div
                class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
            >
                <p
                    class="mb-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                >
                    Booking Hari Ini
                </p>
                <h3 class="text-4xl font-black text-[#2D3A4B]">
                    {{ stats.bookings_today }}
                </h3>
            </div>
            <div
                class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
            >
                <p
                    class="mb-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                >
                    Unit Banned
                </p>
                <h3 class="text-4xl font-black text-[#2D3A4B]">
                    {{ stats.banned_units }}
                </h3>
            </div>
        </div>

        <!-- SCHEDULE BOARD -->
        <div
            class="flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm"
        >
            <!-- Legend -->
            <div
                class="z-10 flex flex-col justify-between gap-4 border-b border-slate-100 bg-white p-6 md:flex-row md:items-center"
            >
                <h3 class="text-lg font-bold text-[#2D3A4B]">
                    Monthly Schedule
                </h3>
                <div
                    class="flex flex-wrap gap-3 text-[10px] font-bold tracking-wider uppercase"
                >
                    <div class="flex items-center gap-1">
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        ></span>
                        Check-in
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        Booked
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                        No Show
                    </div>
                    <div class="flex items-center gap-1 pr-2">
                        <span class="h-2 w-2 rounded-full bg-slate-500"></span>
                        Maintenance
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Expired
                    </div>
                </div>
            </div>

            <div
                class="custom-scrollbar relative h-[65vh] w-full overflow-auto bg-slate-50/50"
            >
                <div class="min-w-max">
                    <!-- Header -->
                    <div class="sticky top-0 z-30 flex shadow-sm">
                        <div
                            class="sticky top-0 left-0 z-40 flex w-28 flex-none items-center justify-center border-r border-b border-slate-200 bg-[#F8FAFC] p-4 text-center"
                        >
                            <span
                                class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >JAM</span
                            >
                        </div>
                        <div
                            v-for="day in days"
                            :key="day.date"
                            class="w-32 flex-none border-r border-b border-slate-100 bg-[#F8FAFC] p-3 text-center"
                            :class="day.is_today ? 'bg-blue-50/80' : ''"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                {{ day.day_name.substring(0, 3) }}
                            </div>
                            <div
                                class="text-sm font-black text-[#2D3A4B]"
                                :class="day.is_today ? 'text-[#00ACC1]' : ''"
                            >
                                {{ day.formatted }}
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="divide-y divide-slate-100">
                        <div
                            v-for="hour in 16"
                            :key="hour"
                            class="group flex transition-colors hover:bg-white"
                        >
                            <div
                                class="sticky left-0 z-20 flex w-28 flex-none items-center justify-center border-r border-slate-200 bg-white p-2 text-[10px] font-bold text-slate-500 shadow-[2px_0_5px_rgba(0,0,0,0.02)] group-hover:bg-[#F8FAFC]"
                            >
                                {{ String(hour + 5).padStart(2, '0') }}:00 -
                                {{ String(hour + 6).padStart(2, '0') }}:00
                            </div>

                            <div
                                v-for="day in days"
                                :key="day.date + hour"
                                class="h-24 w-32 flex-none border-r border-slate-100 bg-white/50 p-1.5"
                            >
                                <!-- 1. JIKA ADA BOOKING / MAINTENANCE -->
                                <div
                                    v-if="getBooking(day.date, hour + 5)"
                                    class="flex h-full w-full cursor-pointer flex-col justify-center rounded-xl border p-2 text-[10px] font-bold shadow-sm transition-transform hover:scale-[1.02]"
                                    :class="
                                        getSlotStyle(
                                            getBooking(day.date, hour + 5),
                                        )
                                    "
                                >
                                    <div
                                        v-if="
                                            getBooking(day.date, hour + 5)
                                                .status === 'maintenance'
                                        "
                                    >
                                        <div
                                            class="mb-1 flex items-center gap-1"
                                        >
                                            <span class="text-base">🛠️</span>
                                            <span>MAINTENANCE</span>
                                        </div>
                                        <p
                                            class="line-clamp-2 text-[9px] leading-tight font-normal italic opacity-80"
                                        >
                                            {{
                                                getBooking(day.date, hour + 5)
                                                    .description
                                            }}
                                        </p>
                                    </div>
                                    <div v-else>
                                        <div
                                            class="mb-0.5 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-[9px] uppercase opacity-70"
                                                >UNIT</span
                                            >
                                            <span
                                                v-if="
                                                    getBooking(
                                                        day.date,
                                                        hour + 5,
                                                    ).status === 'no_show'
                                                "
                                                class="rounded bg-white/50 px-1 text-[8px]"
                                                >NOSHOW</span
                                            >
                                        </div>
                                        <p class="truncate text-xs font-black">
                                            {{
                                                getBooking(day.date, hour + 5)
                                                    .unit
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <!-- 2. JIKA WAKTU SUDAH LEWAT (EXPIRED) -->
                                <div
                                    v-else-if="isPast(day.date, hour + 5)"
                                    class="flex h-full w-full cursor-not-allowed flex-col items-center justify-center rounded-xl border border-transparent bg-slate-100/50 opacity-50"
                                >
                                    <span class="text-xl grayscale">🔒</span>
                                </div>

                                <!-- 3. JIKA AVAILABLE (KLIK UNTUK BOOKING) -->
                                <div
                                    v-else
                                    @click="
                                        openBookingModal(
                                            day.date,
                                            hour + 5,
                                            day.formatted,
                                        )
                                    "
                                    class="group/slot flex h-full w-full cursor-pointer flex-col items-center justify-center rounded-xl border border-transparent transition-all hover:border-dashed hover:border-[#1A5F7A] hover:bg-blue-50"
                                >
                                    <span
                                        class="mb-1 text-2xl font-bold text-[#1A5F7A] opacity-0 group-hover/slot:opacity-100"
                                        >+</span
                                    >
                                    <span
                                        class="text-[8px] font-bold text-[#1A5F7A] uppercase opacity-0 group-hover/slot:opacity-100"
                                        >Booking</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL MANUAL BOOKING -->
        <div
            v-if="showBookingModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#1A5F7A]/20 p-4 backdrop-blur-sm transition-opacity"
        >
            <div
                class="w-full max-w-sm overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl ring-1 ring-slate-100"
            >
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-black text-[#1A5F7A]">
                        Manual Booking
                    </h3>
                    <p class="mt-1 text-sm font-bold text-slate-500">
                        {{ formBooking.displayDate }}
                        <span class="mx-1">•</span>
                        {{ formBooking.displayTime }}
                    </p>
                </div>

                <form @submit.prevent="submitBooking" class="space-y-5">
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Pilih Penghuni / Unit</label
                        >
                        <select
                            v-model="formBooking.unit_id"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-[#2D3A4B] transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                            required
                        >
                            <option value="" disabled>-- Pilih Unit --</option>
                            <option
                                v-for="unit in units"
                                :key="unit.id"
                                :value="unit.id"
                            >
                                {{ unit.unit_number }} -
                                {{ unit.owner_name || 'No Name' }}
                            </option>
                        </select>
                        <p
                            v-if="units.length === 0"
                            class="mt-2 text-[10px] text-red-500 italic"
                        >
                            Data unit kosong. Tambahkan unit dulu di menu Unit.
                        </p>
                    </div>

                    <div
                        v-if="formBooking.errors.modal"
                        class="rounded-xl bg-red-50 p-3 text-center text-xs font-bold text-red-600"
                    >
                        ⚠️ {{ formBooking.errors.modal }}
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showBookingModal = false"
                            class="flex-1 rounded-2xl py-3.5 font-bold text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            :disabled="formBooking.processing"
                            class="flex-1 rounded-2xl bg-[#1A5F7A] py-3.5 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] active:scale-95 disabled:opacity-70"
                        >
                            <span v-if="formBooking.processing"
                                >Loading...</span
                            >
                            <span v-else>Booking</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 5px;
    border: 2px solid #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
