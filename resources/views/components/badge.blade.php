@props([
    'color' => 'indigo',
    'size' => 'md',
])

@php
$colorClasses = match($color) {
    'emerald', 'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/40',
    'rose', 'danger', 'red' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/40',
    'amber', 'warning', 'yellow' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/40',
    'sky', 'blue' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/40',
    'slate', 'gray' => 'bg-slate-100 text-slate-800 dark:bg-slate-800/60 dark:text-slate-300 border border-slate-200 dark:border-slate-700',
    default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/40',
};

$sizeClasses = match($size) {
    'sm' => 'px-2 py-0.5 text-xs font-medium',
    'lg' => 'px-3.5 py-1.5 text-sm font-semibold',
    default => 'px-2.5 py-1 text-xs font-semibold',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full {$colorClasses} {$sizeClasses}"]) }}>
    {{ $slot }}
</span>
