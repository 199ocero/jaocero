<x-container class="flex max-w-2xl flex-col gap-10 py-16 sm:py-24">
    <x-illustrations.paper-plane class="w-48 self-end text-fg/70 motion-safe:animate-fly sm:-mb-14" />

    <x-page-header
        eyebrow="say hello"
        doodle="sparkle"
        title="Let's talk"
        description="Have a project, a role, or just a question? Send a message and I'll get back to you."
    >
        @if ($about->email)
            <p class="text-sm text-muted">Prefer email? Write to <a href="mailto:{{ $about->email }}" class="font-medium text-accent hover:underline">{{ $about->email }}</a>.</p>
        @endif
    </x-page-header>

    @if ($sent)
        <div role="status" class="relative rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-6">
            <x-doodles.heart class="absolute -top-3 -right-3 size-8 rotate-12 text-accent motion-safe:animate-float" style="--tilt: 12deg" />
            <h2 class="font-semibold">Thanks, your message is on its way.</h2>
            <p class="mt-1 text-sm text-muted">I'll reply to the email address you gave as soon as I can.</p>
            <button type="button" wire:click="$set('sent', false)" class="mt-4 text-sm font-medium text-accent hover:underline">Send another message</button>
        </div>
    @else
        <form wire:submit="send" class="flex flex-col gap-5" novalidate>
            @error('form')
                <p role="alert" class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm">{{ $message }}</p>
            @enderror

            <div class="grid gap-5 sm:grid-cols-2">
                @include('partials.form-field', ['name' => 'name', 'label' => 'Name', 'autocomplete' => 'name'])
                @include('partials.form-field', ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'autocomplete' => 'email'])
            </div>

            @include('partials.form-field', ['name' => 'company', 'label' => 'Company', 'optional' => true, 'autocomplete' => 'organization'])

            @include('partials.form-field', ['name' => 'message', 'label' => 'Message', 'textarea' => true])

            {{-- Honeypot: hidden from people and screen readers. --}}
            <div class="hidden" aria-hidden="true">
                <label for="website">Website</label>
                <input id="website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-accent px-6 py-3 text-sm font-semibold text-on-accent hover:bg-accent-strong disabled:opacity-60" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="send">Send message</span>
                    <span wire:loading wire:target="send">Sending…</span>
                </button>
            </div>
        </form>
    @endif
</x-container>
