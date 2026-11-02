<x-container class="flex max-w-3xl flex-col gap-16 py-16 sm:py-24">
    <section class="flex flex-col gap-6 sm:flex-row sm:items-start">
        @if ($about->avatarUrl())
            <img src="{{ $about->avatarUrl() }}" alt="{{ config('portfolio.owner_name') }}" class="size-24 shrink-0 rounded-2xl border border-line object-cover">
        @endif

        <x-page-header eyebrow="hello, nice to meet you" doodle="heart" :title="config('portfolio.owner_name')" :description="$about->headline" class="flex-1">
            <dl class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted">
                @if ($about->location)
                    <div class="flex gap-1.5"><dt class="text-subtle">Location</dt><dd>{{ $about->location }}</dd></div>
                @endif
                <div class="flex gap-1.5">
                    <dt class="text-subtle">Availability</dt>
                    <dd>{{ $about->open_to_work ? 'Open to work' : 'Not looking right now' }}</dd>
                </div>
            </dl>
        </x-page-header>

        <div class="hidden w-56 shrink-0 sm:block" data-reveal style="--reveal-delay: 150ms">
            <x-illustrations.workspace class="w-full text-fg/80" />
        </div>
    </section>

    @if ($about->bioParagraphs())
        <section aria-label="Bio" class="flex flex-col gap-4 text-lg text-muted" data-reveal>
            @foreach ($about->bioParagraphs() as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach

            <div class="flex flex-wrap gap-3 pt-2 text-base">
                @if ($about->resumeUrl())
                    <a href="{{ $about->resumeUrl() }}" download class="rounded-full bg-accent px-5 py-2.5 text-sm font-semibold text-on-accent hover:bg-accent-strong">Download resume</a>
                @endif
                <a href="{{ route('contact') }}" wire:navigate class="rounded-full border border-line bg-elevated px-5 py-2.5 text-sm font-semibold text-fg hover:border-accent hover:text-accent">Get in touch</a>
            </div>
        </section>
    @endif

    <section aria-labelledby="quick-answers-heading" class="flex flex-col gap-6" data-reveal>
        <h2 id="quick-answers-heading" class="flex items-center gap-2 text-2xl font-extrabold tracking-[-0.03em]">Quick answers <x-doodles.sparkle class="size-6 text-accent" /></h2>
        <div class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            @foreach ($quickAnswers as $item)
                <div wire:key="quick-answer-{{ $loop->index }}" class="rounded-2xl border border-line bg-elevated p-5">
                    <h3 class="font-semibold">{{ $item['question'] }}</h3>
                    <div class="prose-content mt-2 text-[0.95rem] leading-relaxed">{{ \App\Support\Markdown::render($item['answer']) }}</div>
                </div>
            @endforeach
        </div>
    </section>

    @if ($skillsByCategory->isNotEmpty())
        <section aria-labelledby="skills-heading" class="flex flex-col gap-6" data-reveal>
            <h2 id="skills-heading" class="flex items-center gap-2 text-2xl font-extrabold tracking-[-0.03em]">Skills <x-doodles.star class="size-6 text-accent" /></h2>
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach ($skillsByCategory as $category => $skills)
                    <div wire:key="skills-{{ $category }}">
                        <h3 class="text-sm font-medium text-subtle">{{ $category }}</h3>
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($skills as $skill)
                                <li><x-tag>{{ $skill->name }}</x-tag></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($experiences->isNotEmpty())
        <section aria-labelledby="experience-heading" class="flex flex-col gap-6" data-reveal>
            <h2 id="experience-heading" class="flex items-center gap-2 text-2xl font-extrabold tracking-[-0.03em]">Experience <x-doodles.bolt class="size-6 text-accent" /></h2>
            <ol class="relative flex flex-col gap-8 border-l border-line pl-6">
                @foreach ($experiences as $experience)
                    <li wire:key="experience-{{ $experience->id }}" class="relative">
                        <span @class([
                            'absolute top-1.5 -left-[1.8rem] size-2.5 rounded-full ring-4 ring-canvas',
                            'bg-accent' => $experience->isCurrent(),
                            'bg-line' => ! $experience->isCurrent(),
                        ]) aria-hidden="true"></span>
                        <p class="text-xs text-subtle">{{ $experience->period() }}</p>
                        <h3 class="mt-1 font-semibold">{{ $experience->role }} <span class="font-normal text-muted">at {{ $experience->company }}</span></h3>
                        @if ($experience->description)
                            <p class="mt-2 text-muted">{{ $experience->description }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
</x-container>
