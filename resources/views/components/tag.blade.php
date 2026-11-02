@props(['href' => null, 'active' => false])

@php
    $classes = 'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium transition '
        .($active ? 'border-accent bg-accent text-on-accent' : 'border-line bg-elevated text-muted');
@endphp

@if ($href)
    <a href="{{ $href }}" wire:navigate {{ $attributes->class([$classes, 'hover:border-accent hover:text-accent' => ! $active]) }}>{{ $slot }}</a>
@else
    <span {{ $attributes->class($classes) }}>{{ $slot }}</span>
@endif
