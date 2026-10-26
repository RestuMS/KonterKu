<x-layouts.auth title="Masuk">
    <div class="mb-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Masuk ke Akun</h3>
        <p class="text-xs text-slate-500 mt-1">Masukkan kredensial Anda untuk mengakses sistem konter.</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4.5">
        @csrf

        <x-input
            label="Alamat Email"
            name="email"
            type="email"
            placeholder="admin@konterku.com"
            value="{{ old('email') }}"
            required
            autofocus
        />

        <x-input
            label="Kata Sandi (Password)"
            name="password"
            type="password"
            placeholder="••••••••"
            required
        />

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600 dark:text-slate-400">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span>Ingat saya</span>
            </label>
        </div>

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full py-2.5">
                Masuk ke KonterKu
            </x-button>
        </div>
    </form>

    <div class="mt-6 p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/40 rounded-xl text-xs text-indigo-900 dark:text-indigo-200">
        <span class="font-semibold block mb-0.5">Akun Default Pengujian:</span>
        Email: <code class="font-mono font-semibold">admin@konterku.com</code> | Password: <code class="font-mono font-semibold">admin123</code>
    </div>
</x-layouts.auth>
