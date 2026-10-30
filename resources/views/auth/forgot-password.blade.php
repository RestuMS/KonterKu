<x-layouts.auth title="Lupa Kata Sandi">
    <div>
        <div class="mb-6">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Lupa Kata Sandi?
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Masukkan alamat email terdaftar akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4.5">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                    Alamat Email Terdaftar <span class="text-rose-500">*</span>
                </label>
                <div class="relative rounded-2xl shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                        </svg>
                    </div>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="pemilik@konterku.com"
                        required
                        autofocus
                        class="block w-full rounded-2xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-xs sm:text-sm py-2.5 pl-10 pr-3.5 transition-all {{ $errors->has('email') ? 'border-rose-400 ring-2 ring-rose-500/20' : '' }}"
                    />
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 hover:from-indigo-500 hover:via-indigo-600 hover:to-indigo-700 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/40 active:scale-[0.99] transition-all flex items-center justify-center gap-2 group cursor-pointer"
                >
                    <span>Kirim Tautan Reset Sandi</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-200/80 dark:border-slate-700/80 text-center">
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Ingat kata sandi Anda?
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 hover:underline">
                    Kembali ke halaman Masuk
                </a>
            </p>
        </div>
    </div>
</x-layouts.auth>
