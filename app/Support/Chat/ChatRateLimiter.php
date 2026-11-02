<?php

namespace App\Support\Chat;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Per-visitor limits on chat messages, to keep AI costs predictable.
 * Limits are configured in config/portfolio.php (chat.rate_limit).
 */
class ChatRateLimiter
{
    private const int MINUTE = 60;

    private const int DAY = 86400;

    public function __construct(private string $visitorKey) {}

    /**
     * A friendly message when the visitor is over a limit, otherwise null.
     */
    public function exceededMessage(): ?string
    {
        if (RateLimiter::tooManyAttempts($this->dailyKey(), $this->dailyLimit())) {
            return "You've reached today's message limit. Please come back tomorrow, or use the contact page to reach out directly.";
        }

        if (RateLimiter::tooManyAttempts($this->minuteKey(), $this->minuteLimit())) {
            $seconds = RateLimiter::availableIn($this->minuteKey());

            return "You're sending messages a little fast. Please wait {$seconds} seconds and try again.";
        }

        return null;
    }

    public function hit(): void
    {
        RateLimiter::hit($this->minuteKey(), self::MINUTE);
        RateLimiter::hit($this->dailyKey(), self::DAY);
    }

    private function minuteKey(): string
    {
        return "chat:minute:{$this->visitorKey}";
    }

    private function dailyKey(): string
    {
        return "chat:day:{$this->visitorKey}";
    }

    private function minuteLimit(): int
    {
        return (int) config('portfolio.chat.rate_limit.per_minute');
    }

    private function dailyLimit(): int
    {
        return (int) config('portfolio.chat.rate_limit.per_day');
    }
}
