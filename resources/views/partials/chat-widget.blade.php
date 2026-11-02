{{--
    Floating chat for every page except home (which has the chat built into the hero).
    It shares the visitor's session conversation, and @persist keeps it open, even mid-answer,
    while they navigate with wire:navigate.
--}}
@unless (request()->routeIs('home'))
    @persist('chat-widget')
        <div
            x-data="{ open: false, greeted: false }"
            x-init="setTimeout(() => greeted = true, 2500)"
            x-effect="document.body.classList.toggle('max-sm:overflow-hidden', open)"
            x-on:chat-widget-close.window="open = false"
            x-on:keydown.escape.window="open = false"
            class="fixed right-4 bottom-4 z-50 flex flex-col items-end gap-3 sm:right-6 sm:bottom-6"
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
                class="fixed inset-0 origin-bottom-right overflow-hidden bg-elevated sm:static sm:inset-auto sm:h-[min(38rem,calc(100dvh-8rem))] sm:w-[25rem] sm:rounded-[1.75rem] sm:border sm:border-line sm:shadow-[0_24px_70px_-20px_rgb(0_0_0/0.3)]"
            >
                <livewire:chat compact />
            </div>

            <div class="flex items-center gap-2" x-bind:class="open && 'max-sm:hidden'">
                <x-hand-note
                    x-show="greeted && ! open"
                    x-cloak
                    x-transition.opacity.duration.500ms
                    class="hidden -rotate-3 text-accent sm:inline-flex"
                >
                    psst… still here
                    <x-scribble-arrow class="mb-1 ml-1 inline w-8 text-accent/80" />
                </x-hand-note>

                <button
                    type="button"
                    x-on:click="open = ! open; greeted = false; if (open) $dispatch('chat-widget-opened')"
                    x-bind:aria-expanded="open"
                    aria-controls="chat-widget-panel"
                    class="group flex items-center gap-2 rounded-full bg-accent py-3 pr-5 pl-4 font-semibold text-on-accent shadow-[0_12px_30px_-10px_var(--accent)] transition hover:-translate-y-0.5 hover:bg-accent-strong"
                >
                    <span class="relative grid size-6 place-items-center" aria-hidden="true">
                        <svg x-show="! open" class="size-6 transition group-hover:-rotate-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 8.5h10M7 12.5h6" />
                            <path d="M5 3.5h14a2.5 2.5 0 0 1 2.5 2.5v9a2.5 2.5 0 0 1-2.5 2.5h-7l-5 4v-4H5A2.5 2.5 0 0 1 2.5 15V6A2.5 2.5 0 0 1 5 3.5Z" />
                        </svg>
                        <svg x-show="open" x-cloak class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round">
                            <path d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </span>
                    <span x-text="open ? 'Close' : 'Ask my AI'">Ask my AI</span>
                </button>
            </div>
        </div>
    @endpersist
@endunless
