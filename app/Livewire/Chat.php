<?php

namespace App\Livewire;

use App\Ai\Agents\PortfolioAgent;
use App\Enums\ChatRole;
use App\Models\ChatConversation;
use App\Support\Chat\ChatRateLimiter;
use App\Support\Chat\VisitorFingerprint;
use App\Support\Markdown;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Ai\Streaming\Events\StreamStart;
use Laravel\Ai\Streaming\Events\TextDelta;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Throwable;

/**
 * The home page chatbox. Streams answers from the PortfolioAgent word by word
 * and logs every exchange to ChatConversation / ChatMessage.
 *
 * @property-read ChatConversation|null $conversation
 */
class Chat extends Component
{
    private const string SESSION_KEY = 'chat.conversation_id';

    /**
     * @var list<string>
     */
    public const array SUGGESTED_QUESTIONS = [
        'What have you built?',
        "What's your tech stack?",
        'Are you open to work?',
        'Show me your latest blog post',
    ];

    /**
     * Quick follow-ups offered under the composer once a conversation has started, keyed by chip label.
     *
     * @var array<string, string>
     */
    public const array FOLLOW_UP_QUESTIONS = [
        'Tell me more' => 'Tell me more about that.',
        'Best project' => 'Which project are you most proud of, and why?',
        'Get in touch' => 'How can I get in touch with you?',
    ];

    /**
     * A friendly error shown under the conversation (validation, limits, provider failures).
     */
    public string $error = '';

    /**
     * Compact mode fills the floating chat widget instead of the home page hero.
     */
    public bool $compact = false;

    public function send(string $text): void
    {
        $this->error = '';
        $text = trim($text);

        if (($validationError = $this->validationError($text)) !== null) {
            $this->error = $validationError;

            return;
        }

        if ($this->hasReachedTurnLimit()) {
            $this->error = 'This conversation has reached its limit. Start a new chat to keep asking questions.';

            return;
        }

        $rateLimiter = new ChatRateLimiter(VisitorFingerprint::fromIp(request()->ip()));

        if (($rateLimitMessage = $rateLimiter->exceededMessage()) !== null) {
            $this->error = $rateLimitMessage;

            return;
        }

        $rateLimiter->hit();

        $conversation = $this->conversation ?? $this->startConversation();

        try {
            $answer = $this->streamAnswer($conversation, $text);
        } catch (Throwable $exception) {
            report($exception);

            $this->error = "Sorry, I couldn't reach the AI right now. Please try again in a moment, or use the contact page.";

            return;
        }

        // Saved only after a successful answer, because the agent replays stored messages as history.
        $conversation->messages()->createMany([
            ['role' => ChatRole::User, 'content' => $text],
            ['role' => ChatRole::Assistant, 'content' => $answer],
        ]);
        $conversation->touch();

        unset($this->conversation);
    }

    public function startNewChat(): void
    {
        session()->forget(self::SESSION_KEY);

        $this->error = '';

        unset($this->conversation);
    }

    /**
     * The visitor's conversation in this session, if they have started one.
     */
    #[Computed]
    public function conversation(): ?ChatConversation
    {
        $conversationId = session(self::SESSION_KEY);

        if (! is_int($conversationId)) {
            return null;
        }

        return ChatConversation::with('messages')->whereKey($conversationId)->first();
    }

    public function render(): View
    {
        return view('livewire.chat', [
            'messages' => $this->conversation->messages ?? collect(),
            'turnLimitReached' => $this->hasReachedTurnLimit(),
        ]);
    }

    private function validationError(string $text): ?string
    {
        $maxLength = (int) config('portfolio.chat.max_message_length');

        $validator = Validator::make(['message' => $text], [
            'message' => ['required', 'string', "max:{$maxLength}"],
        ], [
            'message.required' => 'Type a question first.',
            'message.max' => "Please keep your message under {$maxLength} characters.",
        ]);

        return $validator->errors()->first('message') ?: null;
    }

    private function hasReachedTurnLimit(): bool
    {
        return $this->conversation !== null
            && $this->conversation->userTurns() >= (int) config('portfolio.chat.max_turns');
    }

    private function startConversation(): ChatConversation
    {
        $conversation = ChatConversation::create([
            'session_id' => (string) Str::uuid(),
            'ip_hash' => VisitorFingerprint::fromIp(request()->ip()),
            'user_agent' => Str::limit((string) request()->userAgent(), 250, ''),
        ]);

        session()->put(self::SESSION_KEY, $conversation->id);

        return $conversation;
    }

    /**
     * Stream the answer into the page as it arrives and return the full text.
     *
     * Each chunk replaces the answer with the markdown rendered so far, so the streamed answer has exactly
     * the markup of the saved message that replaces it, and nothing shifts when streaming ends.
     */
    private function streamAnswer(ChatConversation $conversation, string $question): string
    {
        $answer = '';

        foreach ((new PortfolioAgent($conversation))->stream($question) as $event) {
            // Each tool-using step starts a new stream; separate its text from the previous step.
            if ($event instanceof StreamStart && trim($answer) !== '') {
                $answer .= "\n\n";
            }

            if ($event instanceof TextDelta) {
                $answer .= $event->delta;

                // Markdown escapes raw HTML, so the AI can't inject markup.
                $this->stream(to: 'answer', content: (string) Markdown::renderPartial($answer), replace: true);
            }
        }

        return trim($answer) !== ''
            ? trim($answer)
            : "Sorry, I don't have an answer for that. Try rephrasing, or reach out through the contact page.";
    }
}
