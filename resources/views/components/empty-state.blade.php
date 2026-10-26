@props([
    'title' => 'Belum Ada Data',
    'description' => 'Data untuk bagian ini masih kosong atau belum tersedia.',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'py-12 px-6 text-center flex flex-col items-center justify-center']) }}>
    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3.5 shadow-2xs">
        @if($icon)
            {{ $icon }}
        @else
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        @endif
    </div>

    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-1 tracking-tight">
        {{ $title }}
    </h4>

    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mb-4 leading-relaxed">
        {{ $description }}
    </p>

    @if(isset($action))
        <div class="flex items-center gap-2">
            {{ $action }}
        </div>
    @endif
</div>
