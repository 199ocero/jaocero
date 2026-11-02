<footer class="mt-auto">
    <x-container @class(['max-w-4xl border-t border-line', 'py-16 sm:py-20' => $showCallToAction, 'py-8' => ! $showCallToAction])>
        @if ($showCallToAction)
            <div class="relative" data-reveal>
                <x-illustrations.paper-plane class="absolute -top-6 right-0 hidden w-56 text-fg/70 sm:block" />
                <x-hand-note class="-rotate-2 text-accent">still scrolling? let's talk</x-hand-note>
                <h2 class="mt-2 text-4xl font-extrabold tracking-[-0.04em] sm:text-5xl">
                    Got an idea? Let's
                    <span class="relative inline-block whitespace-nowrap">
                        build it.
                        <x-doodles.circle class="absolute -inset-x-4 -inset-y-3 h-[calc(100%+1.5rem)] w-[calc(100%+2rem)] text-accent" style="--draw-delay: 500ms" preserveAspectRatio="none" />
                    </span>
                </h2>
            </div>

            <div class="mt-10 mb-16 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between" data-reveal style="--reveal-delay: 150ms">
                @if ($about->email)
                    <x-copy-email :email="$about->email" />
                @endif
                <a href="{{ route('contact') }}" wire:navigate class="self-start rounded-full bg-accent px-6 py-3 text-sm font-semibold text-on-accent hover:bg-accent-strong sm:self-auto">
                    Send a message
                </a>
            </div>
        @endif

        <div class="flex flex-col gap-4 text-sm text-subtle sm:flex-row sm:items-center sm:justify-between">
            <p>
                &copy; {{ now()->year }} {{ config('portfolio.owner_name') }}
                · <a href="{{ route('feed') }}" class="hover:text-fg">RSS</a>
                · <a href="{{ route('llms.index') }}" class="hover:text-fg">llms.txt</a>
                @if (config('portfolio.source_url'))
                    · <a href="{{ config('portfolio.source_url') }}" class="hover:text-fg" rel="noopener" target="_blank">View source</a>
                @endif
            </p>

            <ul class="flex flex-wrap items-center gap-x-5 gap-y-2">
                @foreach ($about->socialLinks() as $network => $url)
                    <li>
                        <a href="{{ $url }}" class="grid size-9 place-items-center rounded-full border border-line bg-elevated transition hover:-translate-y-0.5 hover:border-accent hover:text-accent" rel="me noopener" target="_blank">
                            <x-social-icon :network="$network" class="size-4" />
                            <span class="sr-only">{{ $network }}</span>
                        </a>
                    </li>
                @endforeach
                @if ($about->email)
                    <li>
                        <a href="mailto:{{ $about->email }}" class="grid size-9 place-items-center rounded-full border border-line bg-elevated transition hover:-translate-y-0.5 hover:border-accent hover:text-accent">
                            <x-icons.mail class="size-4" />
                            <span class="sr-only">Email</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </x-container>
</footer>
