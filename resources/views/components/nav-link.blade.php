@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold leading-5 text-emerald-900 bg-emerald-100 ring-1 ring-emerald-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150 ease-in-out'
            : 'inline-flex items-center rounded-full px-4 py-2 text-sm font-medium leading-5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:text-slate-900 focus:bg-slate-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
