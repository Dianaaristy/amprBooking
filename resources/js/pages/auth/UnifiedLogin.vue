<script setup>
import LoginIllustration from '@/assets/login-tennis.png';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showLoginForm = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login AMPR" />

    <!-- CONTAINER UTAMA - Background DEEP TEAL (#1A5F7A) -->
    <div
        class="flex min-h-screen justify-center overflow-hidden bg-[#1A5F7A] font-sans"
    >
        <div
            class="relative flex h-full min-h-screen w-full max-w-[480px] flex-col bg-[#1A5F7A] shadow-2xl"
        >
            <!-- ===========================
                 HALAMAN 1: WELCOME SCREEN
                 =========================== -->
            <transition name="fade">
                <div
                    v-if="!showLoginForm"
                    class="absolute inset-0 z-10 flex flex-col items-center justify-between bg-gradient-to-b from-[#1A5F7A] to-[#164e63] p-8 pb-12"
                >
                    <!-- Decorative Blobs (Mint Green Light) -->
                    <div
                        class="absolute top-[-60px] right-[-60px] h-64 w-64 rounded-full bg-[#57C5B6]/20 blur-[80px]"
                    ></div>
                    <div
                        class="absolute bottom-20 left-[-40px] h-48 w-48 rounded-full bg-[#57C5B6]/10 blur-[60px]"
                    ></div>

                    <!-- Skip Button (Putih/Mint) -->
                    <div class="relative z-20 w-full pt-4 text-right">
                        <button
                            @click="showLoginForm = true"
                            class="text-sm font-bold text-[#57C5B6] transition hover:text-white"
                        >
                            Skip
                        </button>
                    </div>

                    <!-- Ilustrasi Besar -->
                    <div
                        class="relative flex w-full flex-1 items-center justify-center"
                    >
                        <div
                            class="relative flex aspect-square w-full items-center justify-center"
                        >
                            <div
                                class="absolute inset-0 scale-90 transform animate-pulse rounded-full bg-white/5 blur-3xl"
                            ></div>
                            <img
                                :src="LoginIllustration"
                                class="relative z-10 w-[110%] transform object-contain drop-shadow-2xl transition duration-700 hover:scale-105"
                                alt="Illustration"
                            />
                        </div>
                    </div>

                    <!-- Teks & Tombol -->
                    <div class="relative z-20 w-full space-y-8 text-center">
                        <div class="space-y-2">
                            <p
                                class="text-xs font-bold tracking-[0.2em] text-[#57C5B6] uppercase opacity-80"
                            >
                                Welcome to
                            </p>
                            <h1
                                class="text-4xl leading-none font-black tracking-tight text-white"
                            >
                                Serenity<br /><span class="text-[#57C5B6]"
                                    >Court</span
                                >
                            </h1>
                            <p
                                class="mt-4 px-6 text-sm leading-relaxed font-light text-slate-300"
                            >
                                Rasakan pengalaman booking lapangan tenis yang
                                eksklusif dan mudah.
                            </p>
                        </div>

                        <div class="space-y-4 px-4">
                            <!-- Tombol Utama (Mint Green) -->
                            <button
                                @click="showLoginForm = true"
                                class="w-full transform rounded-[1.5rem] bg-[#57C5B6] py-4 text-lg font-bold text-[#1A5F7A] shadow-xl shadow-[#57C5B6]/20 transition hover:bg-[#4ab3a5] active:scale-95"
                            >
                                Log In
                            </button>
                            <!-- Tombol Outline (Transparan) -->
                            <button
                                class="w-full rounded-[1.5rem] border border-white/20 bg-white/5 py-4 text-lg font-bold text-white backdrop-blur-sm transition hover:bg-white/10"
                            >
                                Sign Up
                            </button>
                        </div>

                        <p
                            class="mx-auto max-w-xs text-[10px] leading-tight text-slate-400 opacity-70"
                        >
                            By continuing, you agree to our
                            <span class="text-[#57C5B6] underline">Terms</span>
                            &
                            <span class="text-[#57C5B6] underline"
                                >Privacy Policy</span
                            >.
                        </p>
                    </div>
                </div>
            </transition>

            <!-- ===========================
                 HALAMAN 2: LOGIN FORM (Slide Up)
                 =========================== -->
            <transition name="slide-up">
                <div
                    v-if="showLoginForm"
                    class="absolute inset-0 z-20 flex flex-col"
                >
                    <!-- Bagian Atas (Gambar & Tombol Back) -->
                    <!-- Background tetap Deep Teal -->
                    <div
                        class="relative flex h-[35%] items-center justify-center overflow-hidden bg-[#1A5F7A]"
                    >
                        <button
                            @click="showLoginForm = false"
                            class="absolute top-8 left-6 z-30 flex items-center gap-2 rounded-full bg-black/20 px-4 py-2 text-sm font-bold text-white backdrop-blur-md transition hover:bg-black/40"
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
                                    stroke-width="3"
                                    d="M15 19l-7-7 7-7"
                                ></path>
                            </svg>
                            Back
                        </button>

                        <!-- Gambar (Lebih Kecil Sedikit) -->
                        <img
                            :src="LoginIllustration"
                            class="absolute bottom-[-15%] w-[60%] object-contain opacity-90 drop-shadow-2xl"
                            alt="Illustration"
                        />

                        <!-- Cahaya Belakang (Glow) -->
                        <div
                            class="absolute top-10 right-[-30px] h-40 w-40 rounded-full bg-[#57C5B6] opacity-40 blur-[80px]"
                        ></div>
                    </div>

                    <!-- Bagian Bawah (Card Putih) -->
                    <div
                        class="animate-slide-content relative z-20 -mt-6 flex h-[75%] flex-col rounded-t-[3rem] bg-[#F5F7F9] p-8 shadow-[0_-20px_60px_rgba(0,0,0,0.3)]"
                    >
                        <div class="mb-8 text-center">
                            <h2 class="text-3xl font-black text-[#1A5F7A]">
                                Welcome back!
                            </h2>
                            <p class="mt-1 text-sm font-medium text-slate-400">
                                Please enter your details.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Input 1 -->
                            <div class="space-y-1.5">
                                <label
                                    class="ml-4 text-xs font-bold tracking-wider text-[#1A5F7A] uppercase opacity-70"
                                    >Email / Unit</label
                                >
                                <input
                                    v-model="form.email"
                                    type="text"
                                    placeholder="Contoh: TWR-A-0101"
                                    class="w-full rounded-[1.5rem] border-none bg-white px-6 py-4 font-bold text-[#1A5F7A] placeholder-slate-300 shadow-sm transition-all focus:ring-2 focus:ring-[#57C5B6]"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 ml-4 text-xs font-bold text-red-500"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Input 2 -->
                            <div class="space-y-1.5">
                                <label
                                    class="ml-4 text-xs font-bold tracking-wider text-[#1A5F7A] uppercase opacity-70"
                                    >Password</label
                                >
                                <div class="relative">
                                    <input
                                        v-model="form.password"
                                        type="password"
                                        placeholder="••••••••"
                                        class="w-full rounded-[1.5rem] border-none bg-white px-6 py-4 font-bold text-[#1A5F7A] placeholder-slate-300 shadow-sm transition-all focus:ring-2 focus:ring-[#57C5B6]"
                                    />
                                    <div
                                        class="absolute top-4 right-6 cursor-pointer text-slate-300 hover:text-[#57C5B6]"
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
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between px-2 pt-1"
                            >
                                <label
                                    class="flex cursor-pointer items-center text-xs font-bold text-slate-400 transition hover:text-[#1A5F7A]"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.remember"
                                        class="mr-2 h-4 w-4 rounded border-gray-300 bg-gray-100 text-[#57C5B6] focus:ring-[#57C5B6]"
                                    />
                                    Remember me
                                </label>
                                <a
                                    href="#"
                                    class="text-xs font-bold text-[#1A5F7A] transition hover:text-[#57C5B6]"
                                    >Forgot password?</a
                                >
                            </div>

                            <!-- Tombol Login (Deep Teal Solid) -->
                            <button
                                :disabled="form.processing"
                                class="mt-2 flex w-full items-center justify-center gap-2 rounded-[1.5rem] bg-[#1A5F7A] py-4 font-bold text-white shadow-xl shadow-[#1A5F7A]/20 transition-all hover:bg-[#154c61] active:scale-95"
                            >
                                <span v-if="form.processing">Memproses...</span>
                                <span v-else>Log In</span>
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active {
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-leave-active {
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    transform: translateY(100%);
}
.slide-up-leave-to {
    transform: translateY(100%);
}

@keyframes slideContent {
    from {
        transform: translateY(40px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-slide-content {
    animation: slideContent 0.6s ease-out 0.3s backwards;
}
</style>
