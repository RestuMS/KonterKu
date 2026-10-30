<x-layouts.auth title="Masuk">
    <div x-data="{
        showPassword: false,
        email: '{{ old('email') }}',
        password: '',
        demoCopied: false,
        fillDemo() {
            this.email = 'admin@konterku.com';
            this.password = 'admin123';
            this.demoCopied = true;
            setTimeout(() => { this.demoCopied = false; }, 3000);
        }
    }">
        <!-- Segmented Tab Navigasi Masuk / Daftar -->
        <div class="flex items-center p-1 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/80 mb-6">
            <a
                href="{{ route('login') }}"
                class="flex-1 py-2.5 text-center text-xs font-bold rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 text-white shadow-md shadow-indigo-600/25 transition-all duration-200"
            >
                Masuk ke Akun
            </a>
            <a
                href="{{ route('register') }}"
                class="flex-1 py-2.5 text-center text-xs font-semibold rounded-xl text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-white hover:bg-white/50 dark:hover:bg-slate-700/50 transition-all duration-200"
            >
                Daftar Akun Baru
            </a>
        </div>

        <!-- Header Form -->
        <div class="mb-6">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-100 dark:border-indigo-900/50 text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2">
                <span>🔐 Portal Akses Toko</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                Masuk ke Akun
                <span class="text-xl animate-bounce">👋</span>
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                Masukkan email dan kata sandi Anda untuk mulai mengelola transaksi konter.
            </p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4.5">
            @csrf

            <!-- Input Email dengan Dynamic Icon Glow -->
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
                        placeholder="admin@konterku.com"
                        required
                        autofocus
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

            <!-- Input Password dengan Toggle Show/Hide & Dynamic Icon Glow -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Kata Sandi (Password) <span class="text-rose-500">*</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 hover:underline transition-colors">
                        Lupa sandi?
                    </a>
                </div>
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
                        placeholder="••••••••"
                        required
                        class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:outline-hidden text-xs sm:text-sm py-2.5 pl-10 pr-10 transition-all {{ $errors->has('password') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                        tabindex="-1"
                        aria-label="Toggle password visibility"
                    >
                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPassword" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1 animate-fadeIn">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Opsi Ingat Saya -->
            <div class="flex items-center justify-between text-xs pt-0.5">
                <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors group">
                    <input
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded-md border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-700 cursor-pointer transition-all"
                    >
                    <span class="font-medium group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Ingat saya pada perangkat ini</span>
                </label>
            </div>

            <!-- Tombol Masuk dengan Shimmer Light Effect -->
            <div class="pt-1.5">
                <button
                    type="submit"
                    class="relative overflow-hidden w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-500 hover:via-indigo-600 hover:to-indigo-700 text-white font-extrabold text-xs sm:text-sm shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 group cursor-pointer"
                >
                    <!-- Shimmer Light Beam -->
                    <span class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent via-white/20 to-transparent group-hover:animate-shine pointer-events-none"></span>

                    <span>Masuk ke KonterKu</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Divider & Beralih ke Registrasi -->
        <div class="mt-6 pt-5 border-t border-slate-200/80 dark:border-slate-700/80 text-center">
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Belum memiliki akun konter?
                <a href="{{ route('register') }}" class="font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 hover:underline">
                    Daftar Akun Baru
                </a>
            </p>
        </div>

        <!-- ============================================================= -->
        <!-- KOTAK AKUN PENGUJIAN DENGAN TOAST NOTIFIKASI SUKSES ISI       -->
        <!-- ============================================================= -->
        <div class="mt-5 p-4 rounded-2xl bg-gradient-to-r from-indigo-50/80 via-white to-sky-50/80 dark:from-slate-800/90 dark:via-indigo-950/30 dark:to-slate-800/90 border border-indigo-100 dark:border-indigo-900/60 shadow-xs relative overflow-hidden">
            <!-- Feedback Toast saat klik Isi Otomatis -->
            <div
                x-show="demoCopied"
                x-transition:enter="transition ease-out duration-200 transform"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="absolute inset-0 bg-emerald-600/95 text-white flex items-center justify-center gap-2 text-xs font-bold z-20 backdrop-blur-xs"
                style="display: none;"
            >
                <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span>Kredensial Pengujian Berhasil Diisi! Silakan Klik Masuk.</span>
            </div>

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-500 animate-ping"></span>
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-indigo-900 dark:text-indigo-300">
                        Akun Default Pengujian:
                    </span>
                </div>
                <button
                    type="button"
                    @click="fillDemo()"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-[10px] font-extrabold bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white shadow-xs hover:shadow-indigo-600/30 active:scale-95 transition-all cursor-pointer"
                    title="Klik untuk mengisi email dan kata sandi pengujian otomatis"
                >
                    <svg class="w-3 h-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>⚡ Isi Otomatis</span>
                </button>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 text-xs text-slate-700 dark:text-slate-300">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 text-[11px]">Email:</span>
                    <code class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 font-mono font-bold text-xs select-all">admin@konterku.com</code>
                </div>
                <span class="hidden sm:inline text-slate-300 dark:text-slate-600">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400 text-[11px]">Password:</span>
                    <code class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-indigo-600 dark:text-indigo-400 font-mono font-bold text-xs select-all">admin123</code>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 italic leading-relaxed">
                *Akun pengujian tetap aktif untuk uji coba lokal di Laragon. Untuk keperluan hosting kedepannya, gunakan tombol "Daftar Akun Baru" di atas.
            </p>
        </div>
    </div>
</x-layouts.auth>
