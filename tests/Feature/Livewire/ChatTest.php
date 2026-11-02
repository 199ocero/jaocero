<?php

use App\Ai\Agents\PortfolioAgent;
use App\Enums\ChatRole;
use App\Livewire\Chat;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Support\Chat\VisitorFingerprint;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Ai\Prompts\AgentPrompt;
use Livewire\Livewire;

beforeEach(function () {
    RateLimiter::clear('chat:minute:'.VisitorFingerprint::fromIp('127.0.0.1'));
    RateLimiter::clear('chat:day:'.VisitorFingerprint::fromIp('127.0.0.1'));
});

/**
 * Livewire streams chunks by echoing and flushing JSON payloads. Collect them during
 * the call and return the streamed content exactly as the browser would insert it.
 */
function sendChatMessage(mixed $component, string $text): string
{
    $output = '';

    ob_start(function (string $chunk) use (&$output): string {
        $output .= $chunk;

        return '';
    });

    try {
        $component->call('send', $text);
    } finally {
        ob_end_clean();
    }

    preg_match_all('/\{"stream":true.*?"endStream":true\}/', $output, $payloads);

    return collect($payloads[0])
        ->map(fn (string $payload): string => json_decode($payload, true)['body']['content'])
        ->implode('');
}

it('shows the suggested questions and storage note before the first message', function () {
    Livewire::test(Chat::class)
        ->assertSee('What have you built?')
        ->assertSee("What's your tech stack?")
        ->assertSee('Are you open to work?')
        ->assertSee('Show me your latest blog post')
        ->assertSee('Conversations may be stored to improve this site.');
});

it('streams the answer and logs both messages', function () {
    PortfolioAgent::fake(['Sam built **Helpdesk Copilot**.'])->preventStrayPrompts();

    $component = Livewire::test(Chat::class);
    $streamed = sendChatMessage($component, 'What have you built?');

    $component->assertSet('error', '')
        ->assertSee('<strong>Helpdesk Copilot</strong>', false);

    expect($streamed)->toBe('Sam built **Helpdesk Copilot**.');

    $conversation = ChatConversation::sole();

    expect($conversation->messages->map(fn (ChatMessage $message) => [$message->role, $message->content])->all())->toBe([
        [ChatRole::User, 'What have you built?'],
        [ChatRole::Assistant, 'Sam built **Helpdesk Copilot**.'],
    ]);
});

it('escapes HTML in streamed chunks', function () {
    PortfolioAgent::fake(['<img src=x onerror=alert(1)>']);

    $streamed = sendChatMessage(Livewire::test(Chat::class), 'Hello');

    expect($streamed)
        ->not->toContain('<img src=x')
        ->toContain('&lt;img src=x');
});

it('stores a hash of the visitor IP instead of the raw address', function () {
    PortfolioAgent::fake(['Hi!']);

    sendChatMessage(Livewire::test(Chat::class), 'Hello');

    expect(ChatConversation::sole()->ip_hash)
        ->toBe(VisitorFingerprint::fromIp('127.0.0.1'))
        ->not->toContain('127.0.0.1');
});

it('keeps the conversation history within the session', function () {
    PortfolioAgent::fake(fn (string $prompt): string => "Answer to: {$prompt}");

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'First question');
    sendChatMessage($component, 'Second question');

    $conversation = ChatConversation::sole();

    PortfolioAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('Second question')
        && $prompt->agent instanceof PortfolioAgent
        && $prompt->agent->conversation?->is($conversation) === true);

    expect(ChatMessage::count())->toBe(4)
        ->and(ChatMessage::count())->toBe(4);

    $component->assertSeeInOrder(['First question', 'Answer to: First question', 'Second question', 'Answer to: Second question']);
});

it('starts a fresh conversation when the visitor asks for a new chat', function () {
    PortfolioAgent::fake(['First answer.', 'Second answer.']);

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'First question');

    $component->call('startNewChat')->assertDontSee('First question');
    sendChatMessage($component, 'Second question');

    expect(ChatConversation::count())->toBe(2);
});

it('rejects empty and overly long messages without calling the AI', function (string $text, string $error) {
    config()->set('portfolio.chat.max_message_length', 500);
    PortfolioAgent::fake();

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, $text);

    $component->assertSet('error', $error);
    PortfolioAgent::assertNeverPrompted();
    expect(ChatMessage::count())->toBe(0);
})->with([
    'empty' => ['   ', 'Type a question first.'],
    'too long' => [str_repeat('a', 501), 'Please keep your message under 500 characters.'],
]);

it('stops the conversation at the maximum number of turns', function () {
    config()->set('portfolio.chat.max_turns', 1);
    PortfolioAgent::fake(['First answer.']);

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'First question');
    sendChatMessage($component, 'Second question');

    $component->assertSet('error', 'This conversation has reached its limit. Start a new chat to keep asking questions.')
        ->assertSee('Start a new chat');
    PortfolioAgent::assertPromptedTimes(1);
});

it('limits how many messages a visitor can send per minute', function () {
    config()->set('portfolio.chat.rate_limit.per_minute', 2);
    PortfolioAgent::fake(['One.', 'Two.', 'Three.']);

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'One');
    sendChatMessage($component, 'Two');
    sendChatMessage($component, 'Three');

    expect($component->get('error'))->toContain("You're sending messages a little fast");
    PortfolioAgent::assertPromptedTimes(2);
});

it('limits how many messages a visitor can send per day', function () {
    config()->set('portfolio.chat.rate_limit.per_day', 1);
    PortfolioAgent::fake(['One.', 'Two.']);

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'One');
    sendChatMessage($component, 'Two');

    expect($component->get('error'))->toContain("You've reached today's message limit");
    PortfolioAgent::assertPromptedTimes(1);
});

it('shows a friendly error and logs nothing when the AI provider fails', function () {
    PortfolioAgent::fake(fn () => throw new RuntimeException('Provider timed out'));

    $component = Livewire::test(Chat::class);
    sendChatMessage($component, 'Hello');

    $component->assertSet('error', "Sorry, I couldn't reach the AI right now. Please try again in a moment, or use the contact page.")
        ->assertSee('Contact page');

    expect(ChatMessage::count())->toBe(0);
});

it('renders the chat on the home page', function () {
    $this->get('/')
        ->assertSeeLivewire(Chat::class)
        ->assertSee('What have you built?');
});
