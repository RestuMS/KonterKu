<x-layouts.app title="Produk & Persediaan">
    <x-slot:header>
        Produk & Persediaan Barang
    </x-slot:header>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Katalog & Stok Produk</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data harga modal, harga jual, dan jumlah stok barang konter.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <x-button href="{{ route('stocks.create') }}" variant="secondary" class="font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Penyesuaian Stok
            </x-button>
            <x-button href="{{ route('products.create') }}" variant="primary" class="font-semibold shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk Baru
            </x-button>
        </div>
    </div>

    <!-- Filter Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-center">
            <div class="lg:col-span-2">
                <x-input
                    name="search"
                    placeholder="Cari kode SKU atau nama produk..."
                    value="{{ request('search') }}"
                />
            </div>
            <div>
                <x-select name="category_id">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
            <div>
                <x-select name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Dijual</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                </x-select>
            </div>
            <div class="flex items-center gap-2">
                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>Stok Menipis</span>
                </label>
                <x-button type="submit" variant="secondary" size="sm" class="ml-auto">
                    Filter
                </x-button>
                @if(request()->hasAny(['search', 'category_id', 'status', 'low_stock']))
                    <a href="{{ route('products.index') }}" class="p-1.5 text-slate-400 hover:text-slate-600" title="Reset">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Products Table -->
    <x-card>
        <div class="overflow-x-auto -mx-6">
            <table class="min-w-full divide-y divide-slate-200/80 dark:divide-slate-700 text-left text-xs">
                <thead class="bg-slate-50/70 dark:bg-slate-850 text-slate-500 uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Kode & Produk</th>
                        <th class="px-6 py-3.5">Kategori</th>
                        <th class="px-6 py-3.5 text-right">Harga Modal</th>
                        <th class="px-6 py-3.5 text-right">Harga Jual</th>
                        <th class="px-6 py-3.5 text-right">Margin / Unit</th>
                        <th class="px-6 py-3.5 text-center">Stok</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 font-medium">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-750/30 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block text-sm">{{ $prod->name }}</span>
                                <span class="font-mono text-[11px] text-slate-400">Kode: {{ $prod->code }}</span>
                            </td>
                            <td class="px-6 py-3.5">
                                <x-badge color="sky">{{ $prod->category->name ?? 'Umum' }}</x-badge>
                            </td>
                            <td class="px-6 py-3.5 text-right text-slate-500">
                                @rupiah($prod->cost_price)
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-slate-900 dark:text-slate-100">
                                @rupiah($prod->selling_price)
                            </td>
                            <td class="px-6 py-3.5 text-right font-semibold text-emerald-600">
                                + @rupiah($prod->selling_price - $prod->cost_price)
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($prod->stock <= $prod->min_stock)
                                    <x-badge color="rose">
                                        {{ $prod->stock }} {{ $prod->unit }} (Min: {{ $prod->min_stock }})
                                    </x-badge>
                                @else
                                    <x-badge color="slate">
                                        {{ $prod->stock }} {{ $prod->unit }}
                                    </x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($prod->is_active)
                                    <x-badge color="emerald">Aktif</x-badge>
                                @else
                                    <x-badge color="slate">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('products.edit', $prod) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 dark:hover:bg-slate-700" title="Edit Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    <button
                                        type="button"
                                        @click="$dispatch('open-modal', 'delete-product-{{ $prod->id }}')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                                        title="Hapus Produk"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>

                                    <!-- Modal Konfirmasi Hapus Produk -->
                                    <x-modal name="delete-product-{{ $prod->id }}" title="Konfirmasi Hapus Produk">
                                        <div class="text-left text-sm text-slate-600 dark:text-slate-400">
                                            <p class="mb-2">Apakah Anda yakin ingin menghapus produk <strong>{{ $prod->name }}</strong> (Kode: {{ $prod->code }})?</p>
                                            <p class="text-xs text-slate-500">Jika produk sudah memiliki riwayat penjualan, produk akan dinonaktifkan secara aman (tidak merusak data keuangan historis).</p>
                                        </div>

                                        <x-slot:footer>
                                            <x-button type="button" variant="secondary" size="sm" @click="$dispatch('close-modal', 'delete-product-{{ $prod->id }}')">
                                                Batal
                                            </x-button>
                                            <form method="POST" action="{{ route('products.destroy', $prod) }}">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" variant="danger" size="sm">
                                                    Ya, Hapus / Nonaktifkan
                                                </x-button>
                                            </form>
                                        </x-slot:footer>
                                    </x-modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-slate-400">
                                Belum ada data produk ditemukan. Klik "Tambah Produk Baru" untuk menambahkan barang!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </x-card>
</x-layouts.app>
