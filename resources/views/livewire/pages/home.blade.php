<div class="flex flex-col gap-28 overflow-x-clip pb-28">
    <section class="pt-16 sm:pt-28">
        <x-container class="relative max-w-4xl">
            {{-- Decorations (desktop): doodles that draw in and a wandering design-tool cursor. --}}
            <div class="pointer-events-none absolute inset-0 hidden md:block" aria-hidden="true" data-reveal>
                <x-doodles.sparkle class="absolute -top-10 right-24 size-11 text-accent motion-safe:animate-float" style="--tilt: 10deg" />
                <x-doodles.burst class="absolute -top-4 right-8 size-8 text-subtle/70" style="--draw-delay: 900ms" />
                <x-doodles.spiral class="absolute top-52 -left-24 size-14 text-subtle/60" style="--draw-delay: 1200ms" />
                <x-design-cursor class="absolute top-10 right-0 motion-safe:animate-cursor" label="you?" />
            </div>

            <div class="relative">
                <x-hand-note arrow="after" class="mb-1 -rotate-3 sm:absolute sm:-top-9 sm:-left-14">hi there!</x-hand-note>

                <h1 class="text-[clamp(2.4rem,6.2vw,4.6rem)] leading-[1.06] font-extrabold tracking-[-0.045em]">
                    <x-reveal-text :text="'Hi, I\'m '.config('portfolio.owner_name').' — '.config('portfolio.headline')" />
                </h1>
            </div>

            <div class="mt-8 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <p class="max-w-xl text-lg text-pretty text-muted sm:text-xl motion-safe:animate-fade-up motion-safe:[animation-delay:700ms]">{{ config('portfolio.tagline') }}</p>
                <x-hand-note class="shrink-0 rotate-2 self-end text-accent motion-safe:animate-fade-up motion-safe:[animation-delay:1000ms]">
                    psst… ask my AI anything
                    <x-scribble-arrow class="ml-1 inline w-9 rotate-[70deg] text-accent/80 motion-safe:animate-wiggle" />
                </x-hand-note>
            </div>
        </x-container>

        <x-container class="relative mt-6 max-w-4xl motion-safe:animate-fade-up motion-safe:[animation-delay:850ms]">
            {{-- Tappable skill stickers floating around the chat (large screens). --}}
            @foreach (array_slice($stickers, 0, 4) as $index => $sticker)
                <x-sticker
                    @class([
                        'absolute z-10 hidden xl:block',
                        'top-20 -left-28' => $index === 0,
                        'top-28 -right-24' => $index === 1,
                        'bottom-28 -left-28' => $index === 2,
                        '-right-16 -bottom-6' => $index === 3,
                    ])
                    :tilt="['-8deg', '7deg', '5deg', '-6deg'][$index]"
                    :color="['sky', 'amber', 'lime', 'rose'][$index]"
                    :delay="($index * 0.7).'s'"
                    wire:key="sticker-{{ $index }}"
                >{{ $sticker }}</x-sticker>
            @endforeach

            <div id="chat">
                <livewire:chat />
            </div>
        </x-container>
    </section>

    @if ($about->bio)
        <section aria-labelledby="about-heading">
            <x-container class="grid max-w-4xl items-center gap-10 md:grid-cols-[1fr_15rem]">
                <div>
                    <x-section-heading id="about-heading" note="a little about me" doodle="heart" :title="'About '.config('portfolio.owner_name')" />
                    <div class="mt-6 space-y-5 text-xl leading-relaxed text-muted" data-reveal style="--reveal-delay: 120ms">
                        @foreach ($about->bioParagraphs() as $paragraph)
                            <p>{{ $paragraph }}</p>
                        @endforeach
                        <a href="{{ route('about') }}" wire:navigate class="group inline-block text-base font-semibold text-accent">
                            More about me <span class="inline-block transition group-hover:translate-x-0.5" aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
                <div class="hidden md:block" data-reveal style="--reveal-delay: 200ms">
                    <x-illustrations.workspace class="w-full text-fg/80" />
                </div>
            </x-container>
        </section>
    @endif

    @if ($featuredProjects->isNotEmpty())
        <section aria-labelledby="projects-heading">
            <x-container class="max-w-4xl">
                <x-section-heading id="projects-heading" note="or just browse…" doodle="sparkle" title="Selected work" :href="route('projects.index')" link-label="All projects" />
                <div class="mt-8 border-b border-line">
                    @foreach ($featuredProjects as $project)
                        <x-project-row :$project wire:key="project-{{ $project->id }}" data-reveal style="--reveal-delay: {{ $loop->index * 90 }}ms" />
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif

    @if ($latestPosts->isNotEmpty())
        <section aria-labelledby="posts-heading">
            <x-container class="max-w-4xl">
                <x-section-heading id="posts-heading" note="fresh from the blog" doodle="bolt" title="Latest writing" :href="route('blog.index')" link-label="All posts" />
                <div class="mt-8 border-b border-line">
                    @foreach ($latestPosts as $post)
                        <x-post-row :$post wire:key="post-{{ $post->id }}" data-reveal style="--reveal-delay: {{ $loop->index * 90 }}ms" />
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif
</div>
