@php
    $maxLength = (int) config('portfolio.chat.max_message_length');
    $ownerName = config('portfolio.owner_name');
    $firstName = \Illuminate\Support\Str::before($ownerName, ' ');
    $title = str_replace('{name}', $ownerName, (string) config('portfolio.chat.title'));
    $subtitle = str_replace('{name}', $ownerName, (string) config('portfolio.chat.subtitle'));
    $isEmpty = $messages->isEmpty();
    $inputId = $compact ? 'chat-widget-input' : 'chat-input';

    // Outline icons for the suggestion chips, cycling if there are more questions than icons.
    $suggestionIcons = [
        'M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.63 8.41m5.96 5.96a14.93 14.93 0 0 1-5.84 2.58m-.12-8.54a6 6 0 0 0-7.38 5.84h4.8m2.58-5.84a14.93 14.93 0 0 0-2.58 5.84m2.7 2.7c-.1.02-.21.04-.31.06a15.1 15.1 0 0 1-2.45-2.45c.02-.1.04-.21.06-.31m-2.24 2.39a4.49 4.49 0 0 0-1.76 4.31 4.49 4.49 0 0 0 4.31-1.76M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z',
        'M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5',
        'M20.25 14.15v4.25c0 1.09-.79 2.04-1.87 2.18-2.09.28-4.22.42-6.38.42s-4.29-.14-6.38-.42c-1.08-.14-1.87-1.09-1.87-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.66V8.7c0-1.08-.77-2.01-1.84-2.17a48.1 48.1 0 0 0-3.41-.39m4.5 8a2.02 2.02 0 0 1-.67.38A23.98 23.98 0 0 1 12 15.75c-2.65 0-5.2-.43-7.58-1.22a2.02 2.02 0 0 1-.67-.38m0 0A2.18 2.18 0 0 1 3 12.49V8.7c0-1.08.77-2.01 1.84-2.17a48.1 48.1 0 0 1 3.41-.39m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.89m7.5 0a48.67 48.67 0 0 0-7.5 0',
        'M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.38c.62 0 1.12.5 1.12 1.13V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.88c0-.62-.5-1.13-1.12-1.13H4.12C3.5 3.75 3 4.25 3 4.88V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z',
    ];
@endphp

{{--
    A minimal chat in the style of Claude / ChatGPT: a centered greeting with the composer right under it,
    then a single column thread where the visitor's messages sit in soft bubbles and the answers read as plain text.
--}}
<section
    x-data="{
        draft: '',
        pending: null,
        answering: false,
        maxLength: {{ $maxLength }},
        init() {
            new MutationObserver(() => {
                this.answering = this.$refs.answer.textContent !== ''
                this.scrollToBottom()
            })
                .observe(this.$refs.answer, { childList: true, characterData: true, subtree: true })
        },
        async submit(text = null) {
            text = (text ?? this.draft).trim()
            if (text === '' || this.pending !== null) return

            this.pending = text
            this.draft = ''
            this.$refs.answer.innerHTML = ''
            this.$nextTick(() => this.scrollToBottom())

            try {
                await $wire.send(text)
            } finally {
                this.pending = null
                this.$nextTick(() => {
                    this.scrollToBottom()
                    this.$refs.input?.focus({ preventScroll: true })
                })
            }
        },
        avatarState() {
            if (this.pending !== null) return this.answering ? 'speaking' : 'thinking'

            return $wire.error !== '' ? 'error' : 'idle'
        },
        scrollToBottom() {
            this.$refs.log.scrollTop = this.$refs.log.scrollHeight
        },
    }"
    aria-label="Chat with {{ $title }}"
    x-on:chat-widget-opened.window="$nextTick(() => $refs.input?.focus())"
    @class([
        'flex flex-col overflow-hidden bg-elevated',
        'h-full' => $compact,
        'h-[min(78dvh,42rem)] rounded-[1.75rem] border border-line shadow-[0_1px_2px_rgb(0_0_0/0.04),0_24px_60px_-28px_rgb(0_0_0/0.18)] dark:shadow-none' => ! $compact,
    ])
>
    {{-- Header --}}
    <header class="flex shrink-0 items-center justify-between gap-3 px-4 py-3">
        <div class="flex min-w-0 items-center gap-2.5">
            <x-robot-head :size="30" x-bind:state="avatarState()" />
            <div class="min-w-0 leading-tight">
                <p class="truncate text-sm font-semibold">{{ $title }}</p>
                @if ($subtitle !== '')
                    <p class="truncate text-xs text-subtle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-0.5">
            @unless ($isEmpty)
                <button type="button" wire:click="startNewChat" x-bind:disabled="pending !== null" class="flex items-center gap-1.5 rounded-full px-2.5 py-1.5 text-xs font-medium text-muted transition hover:bg-surface hover:text-fg disabled:opacity-50">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.86 4.49 1.69-1.69a1.88 1.88 0 1 1 2.65 2.65L10.58 16.07a4.5 4.5 0 0 1-1.9 1.13L6 18l.8-2.68a4.5 4.5 0 0 1 1.13-1.9l8.93-8.93Zm0 0L19.5 7.13M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    New chat
                </button>
            @endunless
            @if ($compact)
                <button type="button" x-on:click="$dispatch('chat-widget-close')" class="grid size-8 place-items-center rounded-full text-muted transition hover:bg-surface hover:text-fg">
                    <span class="sr-only">Close chat</span>
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </header>

    {{-- While the chat is empty, the greeting and the composer sit centered together. --}}
    <div @class(['flex min-h-0 flex-1 flex-col', 'justify-center' => $isEmpty])>
        {{-- Conversation --}}
        <div
            x-ref="log"
            role="log"
            aria-live="polite"
            x-bind:aria-busy="pending !== null"
            @class(['min-h-0 overflow-y-auto overscroll-contain scroll-smooth', 'flex-1' => ! $isEmpty])
            @if ($isEmpty) x-bind:class="{ 'flex-1': pending !== null }" @endif
        >
            <div class="mx-auto flex w-full max-w-3xl flex-col gap-7 px-4 py-6 sm:px-6">
                @if ($isEmpty)
                    <div x-show="pending === null" class="flex flex-col items-center gap-3 pt-2 text-center motion-safe:animate-fade-up">
                        <x-robot-head :size="$compact ? 56 : 64" state="happy" />
                        <h2 @class(['font-semibold tracking-tight text-balance', 'text-xl' => $compact, 'text-2xl sm:text-[1.75rem]' => ! $compact])>Hi, I'm {{ $firstName }}'s AI. How can I help?</h2>
                        <p class="max-w-md text-sm text-pretty text-muted">Ask me anything about {{ $ownerName }}'s work, skills, or availability.</p>
                    </div>
                @endif

                @foreach ($messages as $message)
                    @if ($message->role === \App\Enums\ChatRole::User)
                        <div wire:key="message-{{ $message->id }}" class="flex justify-end motion-safe:animate-message-in">
                            <p class="max-w-[85%] rounded-3xl bg-surface px-4 py-2.5 text-[0.9375rem] leading-6 whitespace-pre-wrap">{{ $message->content }}</p>
                        </div>
                    @else
                        <div wire:key="message-{{ $message->id }}" class="flex items-start gap-3 motion-safe:animate-message-in">
                            <x-robot-head :size="28" paused :interactive="false" class="mt-0.5" />
                            <div class="prose-content min-w-0 flex-1 text-[0.9375rem] leading-7 [&>*+*]:mt-3">
                                {{ \App\Support\Markdown::render($message->content) }}
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- The visitor's message (shown instantly) and the streaming answer. --}}
                <div x-show="pending !== null" x-cloak class="flex flex-col gap-7">
                    <div class="flex justify-end">
                        <p x-text="pending" class="max-w-[85%] rounded-3xl bg-surface px-4 py-2.5 text-[0.9375rem] leading-6 whitespace-pre-wrap"></p>
                    </div>
                    <div class="flex items-start gap-3">
                        <x-robot-head :size="28" :interactive="false" x-bind:state="avatarState()" class="mt-0.5" />
                        <div class="min-w-0 flex-1 text-[0.9375rem] leading-7">
                            <div x-ref="answer" wire:stream="answer" class="peer whitespace-pre-wrap text-fg"></div>
                            <div class="hidden items-center gap-1 pt-2.5 peer-empty:flex" aria-label="The assistant is typing">
                                <span class="size-1.5 animate-bounce rounded-full bg-subtle [animation-delay:-0.3s]"></span>
                                <span class="size-1.5 animate-bounce rounded-full bg-subtle [animation-delay:-0.15s]"></span>
                                <span class="size-1.5 animate-bounce rounded-full bg-subtle"></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($error !== '')
                    <div x-show="pending === null" role="alert" class="flex items-start gap-3">
                        <x-robot-head :size="28" state="error" paused :interactive="false" class="mt-0.5" />
                        <p class="min-w-0 flex-1 rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-sm leading-6">
                            {{ $error }}
                            @if (! $turnLimitReached)
                                <a href="{{ route('contact') }}" wire:navigate class="font-medium text-accent hover:underline">Contact page →</a>
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Composer --}}
        <div class="shrink-0 px-3 pb-3 sm:px-4">
            <div class="mx-auto w-full max-w-3xl">
                @if ($turnLimitReached)
                    <button type="button" wire:click="startNewChat" class="w-full rounded-full bg-fg px-4 py-3 text-sm font-semibold text-canvas transition hover:opacity-85">
                        Start a new chat
                    </button>
                @else
                    <form
                        x-on:submit.prevent="submit()"
                        x-on:click="if ($event.target === $el) $refs.input.focus()"
                        class="cursor-text rounded-[1.6rem] border border-line bg-elevated shadow-[0_2px_10px_-4px_rgb(0_0_0/0.08)] transition focus-within:border-subtle/60 focus-within:shadow-[0_4px_18px_-6px_rgb(0_0_0/0.14)] dark:bg-surface dark:shadow-none"
                    >
                        <label for="{{ $inputId }}" class="sr-only">Your question</label>
                        <textarea
                            id="{{ $inputId }}"
                            x-ref="input"
                            x-model="draft"
                            x-on:keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); submit() }"
                            maxlength="{{ $maxLength }}"
                            rows="1"
                            placeholder="Ask about projects, skills, availability…"
                            class="field-sizing-content block max-h-40 min-h-12 w-full resize-none bg-transparent px-4 pt-3.5 pb-1 text-base text-fg placeholder:text-subtle focus:outline-none sm:text-[0.9375rem]"
                        ></textarea>

                        <div class="flex items-center justify-between gap-3 px-2.5 pb-2.5">
                            <div class="flex min-w-0 gap-1 overflow-x-auto [scrollbar-width:none]">
                                @unless ($isEmpty)
                                    @foreach (\App\Livewire\Chat::FOLLOW_UP_QUESTIONS as $label => $question)
                                        <button
                                            type="button"
                                            wire:key="follow-up-{{ $loop->index }}"
                                            x-on:click="submit(@js($question))"
                                            x-bind:disabled="pending !== null"
                                            class="shrink-0 rounded-full border border-line px-3 py-1.5 text-xs font-medium whitespace-nowrap text-muted transition hover:bg-surface hover:text-fg disabled:opacity-50 dark:hover:bg-elevated"
                                        >{{ $label }}</button>
                                    @endforeach
                                @endunless
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <span x-show="draft.length > maxLength * 0.8" x-cloak x-text="`${draft.length}/${maxLength}`" class="text-xs text-subtle tabular-nums"></span>
                                <button
                                    type="submit"
                                    x-bind:disabled="draft.trim() === '' || pending !== null"
                                    class="grid size-9 place-items-center rounded-full bg-fg text-canvas transition hover:opacity-85 disabled:bg-line disabled:text-subtle disabled:hover:opacity-100"
                                >
                                    <span class="sr-only">Send</span>
                                    <svg class="size-[1.1rem]" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0-6 6m6-6 6 6" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>

                    @if ($isEmpty)
                        <ul x-show="pending === null" class="mt-3 flex flex-wrap justify-center gap-2" aria-label="Suggested questions">
                            @foreach (\App\Livewire\Chat::SUGGESTED_QUESTIONS as $question)
                                <li wire:key="suggestion-{{ $loop->index }}">
                                    <button
                                        type="button"
                                        x-on:click="submit(@js($question))"
                                        @class([
                                            'flex items-center gap-2 rounded-full border border-line text-muted transition hover:bg-surface hover:text-fg',
                                            'px-3 py-1.5 text-xs' => $compact,
                                            'px-3.5 py-2 text-sm' => ! $compact,
                                        ])
                                    >
                                        <svg class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $suggestionIcons[$loop->index % count($suggestionIcons)] }}" />
                                        </svg>
                                        {{ $question }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif

                <p class="mt-2.5 text-center text-[0.7rem] text-subtle">Conversations may be stored to improve this site.</p>
            </div>
        </div>
    </div>
</section>
