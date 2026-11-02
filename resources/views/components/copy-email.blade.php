{{-- Click-to-copy email address, with a mailto fallback when the clipboard isn't available. --}}
@props(['email'])

<button
    type="button"
    x-data="{ copied: false }"
    x-on:click="
        navigator.clipboard?.writeText(@js($email))
            .then(() => { copied = true; setTimeout(() => copied = false, 2000) })
            .catch(() => window.location.href = 'mailto:' + @js($email))
    "
    {{ $attributes->class('group inline-flex flex-col items-start gap-1 text-left') }}
>
    <span class="text-2xl font-bold tracking-[-0.03em] underline decoration-line decoration-2 underline-offset-8 transition group-hover:decoration-accent sm:text-3xl">{{ $email }}</span>
    <span class="hand-note text-lg" x-text="copied ? 'copied! ✓' : 'tap to copy'" aria-live="polite">tap to copy</span>
</button>
