@php
    $maxLength = (int) config('portfolio.chat.max_message_length');
    $ownerName = config('portfolio.owner_name');
@endphp

<section
    x-data="{
        draft: '',
        pending: null,
        maxLength: {{ $maxLength }},
        init() {
            new MutationObserver(() => this.scrollToBottom())
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
        scrollToBottom() {
            this.$refs.log.scrollTop = this.$refs.log.scrollHeight
        },
    }"
    aria-label="Chat with {{ $ownerName }}'s {{ config('portfolio.assistant_name') }}"
    x-on:chat-widget-opened.window="$nextTick(() => $refs.input?.focus())"
    @class([
        'flex flex-col overflow-hidden bg-elevated',
        'h-full' => $compact,
        'h-[min(72dvh,40rem)] rounded-[1.75rem] border border-line shadow-[0_1px_2px_rgb(0_0_0/0.04),0_24px_60px_-24px_rgb(0_0_0/0.18)] dark:shadow-none' => ! $compact,
    ])
>
    {{-- Header --}}
    <header class="flex items-center justify-between gap-3 border-b border-line px-4 py-3">
        <div class="flex items-center gap-3">
            <span class="relative grid size-9 place-items-center rounded-full bg-accent text-xs font-bold text-on-accent" aria-hidden="true">
                AI
                <span class="absolute -right-0.5 -bottom-0.5 size-3 rounded-full border-2 border-elevated bg-emerald-500"></span>
            </span>
            <div class="leading-tight">
                <p class="text-sm font-semibold">{{ $ownerName }}'s {{ config('portfolio.assistant_name') }}</p>
                <p class="text-xs text-subtle">Answers from {{ $ownerName }}'s real projects and experience</p>
            </div>
        </div>

        <div class="flex items-center gap-1">
            @if ($messages->isNotEmpty())
                <button type="button" wire:click="startNewChat" x-bind:disabled="pending !== null" class="rounded-lg px-2.5 py-1.5 text-xs text-muted hover:bg-surface hover:text-fg disabled:opacity-50">
                    New chat
                </button>
            @endif
            @if ($compact)
                <button type="button" x-on:click="$dispatch('chat-widget-close')" class="grid size-8 place-items-center rounded-full text-muted hover:bg-surface hover:text-fg">
                    <span class="sr-only">Close chat</span>
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </header>

    {{-- Conversation --}}
    <div
        x-ref="log"
        role="log"
        aria-live="polite"
        x-bind:aria-busy="pending !== null"
        class="flex flex-1 flex-col gap-4 overflow-y-auto overscroll-contain px-4 py-5 scroll-smooth"
    >
        @if ($messages->isEmpty())
            <div x-show="pending === null" class="draw-on-render m-auto flex max-w-md flex-col items-center gap-4 text-center motion-safe:animate-fade-up">
                <x-illustrations.chatting class="w-32 text-fg/70 sm:w-36" />
                <p class="hand-note text-2xl text-muted">Hi! Ask me anything about {{ $ownerName }}'s work, skills, or availability.</p>
                <ul class="flex flex-wrap justify-center gap-2" aria-label="Suggested questions">
                    @foreach (\App\Livewire\Chat::SUGGESTED_QUESTIONS as $question)
                        <li wire:key="suggestion-{{ $loop->index }}">
                            <button
                                type="button"
                                x-on:click="submit(@js($question))"
                                class="rounded-full border border-line bg-surface px-3.5 py-1.5 text-sm font-medium text-muted transition hover:-translate-y-0.5 hover:border-accent hover:text-accent"
                            >{{ $question }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach ($messages as $message)
            @if ($message->role === \App\Enums\ChatRole::User)
                <div wire:key="message-{{ $message->id }}" class="flex justify-end motion-safe:animate-message-in">
                    <p class="max-w-[85%] rounded-2xl rounded-br-md bg-accent px-4 py-2.5 text-sm whitespace-pre-wrap text-on-accent">{{ $message->content }}</p>
                </div>
            @else
                <div wire:key="message-{{ $message->id }}" class="flex justify-start motion-safe:animate-message-in">
                    <div class="prose-content max-w-[85%] rounded-2xl rounded-bl-md bg-surface px-4 py-2.5 text-sm leading-6 [&>*+*]:mt-3">
                        {{ \App\Support\Markdown::render($message->content) }}
                    </div>
                </div>
            @endif
        @endforeach

        {{-- The visitor's message (shown instantly) and the streaming answer. --}}
        <div x-show="pending !== null" x-cloak class="flex flex-col gap-4">
            <div class="flex justify-end">
                <p x-text="pending" class="max-w-[85%] rounded-2xl rounded-br-md bg-accent px-4 py-2.5 text-sm whitespace-pre-wrap text-on-accent"></p>
            </div>
            <div class="flex justify-start">
                <div class="max-w-[85%] rounded-2xl rounded-bl-md bg-surface px-4 py-2.5 text-sm leading-6">
                    <div x-ref="answer" wire:stream="answer" class="peer whitespace-pre-wrap text-muted"></div>
                    <div class="hidden items-center gap-1 py-1.5 peer-empty:flex" aria-label="The assistant is typing">
                        <span class="size-1.5 animate-bounce rounded-full bg-subtle [animation-delay:-0.3s]"></span>
                        <span class="size-1.5 animate-bounce rounded-full bg-subtle [animation-delay:-0.15s]"></span>
                        <span class="size-1.5 animate-bounce rounded-full bg-subtle"></span>
                    </div>
                </div>
            </div>
        </div>

        @if ($error !== '')
            <div x-show="pending === null" role="alert" class="flex justify-start">
                <p class="max-w-[85%] rounded-2xl rounded-bl-md border border-amber-500/30 bg-amber-500/10 px-4 py-2.5 text-sm">
                    {{ $error }}
                    @if (! $turnLimitReached)
                        <a href="{{ route('contact') }}" wire:navigate class="font-medium text-accent hover:underline">Contact page →</a>
                    @endif
                </p>
            </div>
        @endif
    </div>

    {{-- Composer --}}
    <div class="border-t border-line bg-elevated p-3">
        @if ($turnLimitReached)
            <button type="button" wire:click="startNewChat" class="w-full rounded-full bg-accent px-4 py-3 text-sm font-medium text-on-accent hover:bg-accent-strong">
                Start a new chat
            </button>
        @else
            <form x-on:submit.prevent="submit()" class="flex items-end gap-2">
                <label for="{{ $compact ? 'chat-widget-input' : 'chat-input' }}" class="sr-only">Your question</label>
                <textarea
                    id="{{ $compact ? 'chat-widget-input' : 'chat-input' }}"
                    x-ref="input"
                    x-model="draft"
                    x-on:keydown.enter="if (! $event.shiftKey) { $event.preventDefault(); submit() }"
                    maxlength="{{ $maxLength }}"
                    rows="1"
                    placeholder="Ask about projects, skills, availability…"
                    class="max-h-32 min-h-11 flex-1 resize-none rounded-2xl border border-line bg-surface px-4 py-2.5 text-base text-fg placeholder:text-subtle focus:border-accent focus:ring-2 focus:ring-accent/30 focus:outline-none sm:text-sm"
                ></textarea>
                <button
                    type="submit"
                    x-bind:disabled="draft.trim() === '' || pending !== null"
                    class="grid size-11 shrink-0 place-items-center rounded-full bg-accent text-on-accent transition hover:bg-accent-strong disabled:opacity-40"
                >
                    <span class="sr-only">Send</span>
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" />
                    </svg>
                </button>
            </form>
        @endif

        <p class="mt-2 flex justify-between gap-2 px-1 text-xs text-subtle">
            <span>Conversations may be stored to improve this site.</span>
            <span x-show="draft.length > maxLength * 0.8" x-cloak x-text="`${draft.length}/${maxLength}`"></span>
        </p>
    </div>
</section>
