{{-- A design-tool style collaborator cursor with a name tag. --}}
@props(['label' => 'you?'])

<div {{ $attributes->class('pointer-events-none flex items-start') }} aria-hidden="true">
    <svg class="size-5 text-accent drop-shadow-sm" viewBox="0 0 20 20" fill="currentColor">
        <path d="M3 2.2 16.6 8.5l-6.1 1.9-2.8 6.1L3 2.2Z" stroke="white" stroke-width="1.4" stroke-linejoin="round" />
    </svg>
    <span class="mt-4 -ml-1 rounded-full rounded-tl-sm bg-accent px-2 py-0.5 text-xs font-semibold whitespace-nowrap text-on-accent shadow-sm">{{ $label }}</span>
</div>
