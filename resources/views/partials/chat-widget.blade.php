{{--
    Floating chat for every page except home (which has the chat built into the hero).
    It shares the visitor's session conversation, and @persist keeps it open, even mid-answer,
    while they navigate with wire:navigate. Configured in the admin panel under Site settings.
--}}
@php
    $widget = config('portfolio.chat_widget');
    $isOnLeft = $widget['position'] === \App\Enums\ChatWidgetPosition::BottomLeft->value;
@endphp

@if ($widget['enabled'] && ! request()->routeIs('home'))
    @persist('chat-widget')
        <div
            x-data="{ open: false, greeted: false }"
            x-init="setTimeout(() => greeted = true, 2500)"
            x-effect="document.body.classList.toggle('max-sm:overflow-hidden', open)"
            x-on:chat-widget-close.window="open = false"
            x-on:keydown.escape.window="open = false"
            @class([
                'fixed bottom-4 z-50 flex flex-col gap-3 sm:bottom-6',
                'left-4 items-start sm:left-6' => $isOnLeft,
                'right-4 items-end sm:right-6' => ! $isOnLeft,
            ])
        >
            <div
                id="chat-widget-panel"
                x-show="open"
                x-cloak
                x-transition:enter="transition duration-300 ease-out"
                x-transition:enter-start="translate-y-4 scale-95 opacity-0"
                x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                x-transition:leave="transition duration-150 ease-in"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="translate-y-2 opacity-0"
                @class([
                    'fixed inset-0 overflow-hidden bg-elevated sm:static sm:inset-auto sm:h-[min(38rem,calc(100dvh-8rem))] sm:w-[25rem] sm:rounded-[1.75rem] sm:border sm:border-line sm:shadow-[0_24px_70px_-20px_rgb(0_0_0/0.3)]',
                    'origin-bottom-left' => $isOnLeft,
                    'origin-bottom-right' => ! $isOnLeft,
                ])
            >
                <livewire:chat compact />
            </div>

            <div @class(['flex items-center gap-2', 'flex-row-reverse' => $isOnLeft]) x-bind:class="open && 'max-sm:hidden'">
                @if (filled($widget['note']))
                    <x-hand-note
                        x-show="greeted && ! open"
                        x-cloak
                        x-transition.opacity.duration.500ms
                        @class(['hidden text-accent sm:inline-flex', 'rotate-3' => $isOnLeft, '-rotate-3' => ! $isOnLeft])
                    >
                        @if ($isOnLeft)
                            <x-scribble-arrow class="mr-1 mb-1 inline w-8 -scale-x-100 text-accent/80" />
                        @endif
                        {{ $widget['note'] }}
                        @unless ($isOnLeft)
                            <x-scribble-arrow class="mb-1 ml-1 inline w-8 text-accent/80" />
                        @endunless
                    </x-hand-note>
                @endif

                <button
                    type="button"
                    x-on:click="open = ! open; greeted = false; if (open) $dispatch('chat-widget-opened')"
                    x-bind:aria-expanded="open"
                    aria-controls="chat-widget-panel"
                    class="group flex items-center gap-2 rounded-full bg-accent py-2.5 pr-5 pl-3 font-semibold text-on-accent shadow-[0_12px_30px_-10px_var(--accent)] transition hover:-translate-y-0.5 hover:bg-accent-strong"
                >
                    <span class="relative grid size-8 place-items-center" aria-hidden="true">
                        <x-robot-head :size="32" :interactive="false" x-show="! open" />
                        <svg x-show="open" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <path d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </span>
                    <span x-text="open ? 'Close' : @js($widget['label'])">{{ $widget['label'] }}</span>
                </button>
            </div>
        </div>
    @endpersist
@endif
