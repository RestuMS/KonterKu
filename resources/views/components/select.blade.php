@props([
    'label' => null,
    'name' => null,
    'required' => false,
    'helper' => null,
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-2xs">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm transition-all duration-150 py-2.5 px-3.5 pr-10' . ($errors->has($name) ? ' border-rose-300 ring-2 ring-rose-500/20' : '')
            ]) }}
        >
            {{ $slot }}
        </select>
    </div>

    @if($helper && !$errors->has($name))
        <p class="mt-1 text-xs text-slate-500">{{ $helper }}</p>
    @endif

    @error($name)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5 inline flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
