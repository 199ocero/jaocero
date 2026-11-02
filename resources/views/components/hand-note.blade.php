{{-- A handwritten margin note, optionally paired with a hand-drawn arrow. --}}
@props(['arrow' => null])

<p {{ $attributes->class('hand-note inline-flex items-end gap-1') }}>
    @if ($arrow === 'before')
        <x-scribble-arrow class="mb-1 w-10 -scale-x-100 text-subtle/70" />
    @endif
    <span>{{ $slot }}</span>
    @if ($arrow === 'after')
        <x-scribble-arrow class="mb-1 w-10 text-subtle/70" />
    @endif
</p>
