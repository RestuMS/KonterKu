@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'indigo',
    'trend' => null,
    'trendUp' => true,
])

@php
$colorBg = match($color) {
    'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    'rose' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
    'amber' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
    'sky' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400',
    default => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
};
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 p-5 shadow-xs transition-all duration-200 hover:shadow-md hover:border-slate-300 dark:hover:border-slate-600']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 tracking-wide uppercase">{{ $title }}</span>
        @if($icon)
            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $colorBg }}">
                {{ $icon }}
            </div>
        @endif
    </div>

    <div class="mt-3 flex items-baseline justify-between">
        <div class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
            {{ $value }}
        </div>
    </div>

    @if($subtitle || $trend)
        <div class="mt-2.5 flex items-center text-xs text-slate-500 dark:text-slate-400 gap-1.5">
            @if($trend)
                <span class="font-medium inline-flex items-center gap-0.5 {{ $trendUp ? 'text-emerald-600' : 'text-rose-600' }}">
                    @if($trendUp)
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    @endif
                    {{ $trend }}
                </span>
            @endif
            @if($subtitle)
                <span>{{ $subtitle }}</span>
            @endif
        </div>
    @endif
</div>
