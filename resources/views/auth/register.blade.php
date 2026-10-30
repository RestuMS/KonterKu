<x-layouts.auth title="Daftar Akun Baru">
    <div x-data="{
        name: '{{ old('name') }}',
        phone: '{{ old('phone') }}',
        email: '{{ old('email') }}',
        password: '',
        passwordConfirm: '',
        showPassword: false,
        showPasswordConfirm: false,
        get hasMinLength() {
            return this.password.length >= 6;
        },
        get hasNumber() {
            return /\d/.test(this.password);
        },
        get hasLetter() {
            return /[a-zA-Z]/.test(this.password);
        },
        get strengthScore() {
            let score = 0;
            if (this.hasMinLength) score++;
            if (this.hasNumber) score++;
            if (this.hasLetter) score++;
            return score;
        },
        get isMatch() {
            return this.passwordConfirm.length > 0 && this.password === this.passwordConfirm;
        }
    }">
        <!-- Segmented Tab Navigasi Masuk / Daftar -->
        <div class="flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 mb-6">
            <a
                href="{{ route('login') }}"
                class="flex-1 py-2.5 text-center text-xs font-semibold rounded-xl text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-700/50 transition-all duration-200"
            >
                Masuk ke Akun
            </a>
            <a
                href="{{ route('register') }}"
                class="flex-1 py-2.5 text-center text-xs font-bold rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md shadow-indigo-600/25 transition-all duration-200"
            >
                Daftar Akun Baru
            </a>
        </div>

        <!-- Header Form -->
        <div class="mb-5">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-950/60 dark:to-slate-800 border border-indigo-200/60 dark:border-indigo-800/60 text-[10px] font-extrabold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider mb-2.5 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                <span>Akses Penuh: Administrator / Pemilik Konter</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                Daftar Akun Konter Baru
                <span class="text-xl animate-bounce">🚀</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Buat akun pemilik konter untuk mengelola transaksi keuangan dan persediaan produk Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <!-- Input Nama Lengkap dengan Glow Focus -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <div class="group relative rounded-2xl shadow-2xs transition-all duration-200 focus-within:ring-4 focus-within:ring-indigo-500/15">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        x-model="name"
                        placeholder="Contoh: Budi Santoso"
                        required
                        autofocus
                        class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-3.5 transition-all {{ $errors->has('name') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                    />
                </div>
                @error('name')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 animate-fadeIn">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Nomor HP / WhatsApp -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Nomor HP / WhatsApp
                    </label>
                    <span class="text-[11px] text-slate-400">Opsional</span>
                </div>
                <div class="group relative rounded-2xl shadow-2xs transition-all duration-200 focus-within:ring-4 focus-within:ring-indigo-500/15">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        x-model="phone"
                        placeholder="Contoh: 081234567890"
                        class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-3.5 transition-all {{ $errors->has('phone') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                    />
                </div>
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 animate-fadeIn">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <div class="group relative rounded-2xl shadow-2xs transition-all duration-200 focus-within:ring-4 focus-within:ring-indigo-500/15">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        x-model="email"
                        placeholder="pemilik@konterku.com"
                        required
                        class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-3.5 transition-all {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                    />
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 animate-fadeIn">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Password & Confirm Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <div class="group relative rounded-2xl shadow-2xs transition-all duration-200 focus-within:ring-4 focus-within:ring-indigo-500/15">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            name="password"
                            id="password"
                            x-model="password"
                            placeholder="Min. 6 karakter"
                            required
                            class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-9 transition-all {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                            tabindex="-1"
                            aria-label="Toggle password visibility"
                        >
                            <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPassword" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Ulangi Sandi <span class="text-rose-500">*</span>
                        </label>
                        <!-- Live Match Indicator -->
                        <span x-show="isMatch" class="text-[10px] font-extrabold text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 animate-fadeIn" style="display: none;">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Cocok
                        </span>
                    </div>
                    <div class="group relative rounded-2xl shadow-2xs transition-all duration-200 focus-within:ring-4 focus-within:ring-indigo-500/15">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-600 dark:group-focus-within:text-indigo-400 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            name="password_confirmation"
                            id="password_confirmation"
                            x-model="passwordConfirm"
                            placeholder="Ulangi kata sandi"
                            required
                            class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-9 transition-all"
                        />
                        <button
                            type="button"
                            @click="showPasswordConfirm = !showPasswordConfirm"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                            tabindex="-1"
                            aria-label="Toggle password confirmation visibility"
                        >
                            <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="showPasswordConfirm" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- LIVE PASSWORD STRENGTH METER (Real-Time Visual Feedback) -->
            <div x-show="password.length > 0" x-transition class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 space-y-2">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="font-bold text-slate-600 dark:text-slate-300">Kekuatan Sandi:</span>
                    <span
                        class="font-extrabold font-mono"
                        :class="{
                            'text-rose-500': strengthScore <= 1,
                            'text-amber-500': strengthScore === 2,
                            'text-emerald-500': strengthScore >= 3
                        }"
                        x-text="strengthScore <= 1 ? 'Lemah' : (strengthScore === 2 ? 'Sedang' : 'Kuat & Aman')"
                    ></span>
                </div>
                <!-- Strength 3-Step Progress Bar -->
                <div class="grid grid-cols-3 gap-1.5 h-1.5">
                    <div
                        class="rounded-full transition-all duration-300"
                        :class="strengthScore >= 1 ? (strengthScore === 1 ? 'bg-rose-500' : (strengthScore === 2 ? 'bg-amber-500' : 'bg-emerald-500')) : 'bg-slate-200 dark:bg-slate-700'"
                    ></div>
                    <div
                        class="rounded-full transition-all duration-300"
                        :class="strengthScore >= 2 ? (strengthScore === 2 ? 'bg-amber-500' : 'bg-emerald-500')) : 'bg-slate-200 dark:bg-slate-700'"
                    ></div>
                    <div
                        class="rounded-full transition-all duration-300"
                        :class="strengthScore >= 3 ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'"
                    ></div>
                </div>
                <!-- Real-time Validation Checkmarks -->
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[10px] text-slate-500 dark:text-slate-400 pt-0.5">
                    <span class="flex items-center gap-1" :class="hasMinLength ? 'text-emerald-600 dark:text-emerald-400 font-bold' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Min. 6 Karakter
                    </span>
                    <span class="flex items-center gap-1" :class="hasNumber ? 'text-emerald-600 dark:text-emerald-400 font-bold' : ''">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Mengandung Angka
                    </span>
                </div>
            </div>

            @error('password')
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 animate-fadeIn">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </p>
            @enderror

            <!-- Tombol Submit Pendaftaran dengan Shimmer Light Effect -->
            <div class="pt-2">
                <button
                    type="submit"
                    class="relative overflow-hidden w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-500 hover:via-indigo-600 hover:to-indigo-700 text-white font-extrabold text-xs sm:text-sm shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
                >
                    <!-- Shimmer Light Beam -->
                    <span class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:animate-shine pointer-events-none"></span>

                    <span>Daftar & Mulai Kelola Konter</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Divider & Beralih ke Login -->
        <div class="mt-6 pt-5 border-t border-slate-200/80 dark:border-slate-700/80 text-center">
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 hover:underline">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</x-layouts.auth>
