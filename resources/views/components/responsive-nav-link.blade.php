@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-xl ps-3 pe-4 py-3 text-start text-base font-semibold text-emerald-900 bg-emerald-100 ring-1 ring-inset ring-emerald-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150 ease-in-out'
            : 'block w-full rounded-xl ps-3 pe-4 py-3 text-start text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:text-slate-900 focus:bg-slate-100 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
