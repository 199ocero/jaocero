<?php

use App\Ai\Agents\PortfolioAgent;
use App\Ai\Tools\GetAbout;
use App\Ai\Tools\GetBlogPosts;
use App\Ai\Tools\GetContactInfo;
use App\Ai\Tools\GetExperience;
use App\Ai\Tools\GetProjectDetails;
use App\Ai\Tools\GetProjects;
use App\Ai\Tools\GetSkills;
use App\Ai\Tools\SaveLead;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Laravel\Ai\Messages\MessageRole;
use Laravel\Ai\Prompts\AgentPrompt;

beforeEach(function () {
    config()->set('portfolio.owner_name', 'Sam Rivera');
    config()->set('portfolio.assistant_name', 'AI assistant');
    config()->set('portfolio.site_url', 'https://sam.example.com');
});

it('builds the system prompt from the configured owner details', function () {
    $instructions = (new PortfolioAgent)->instructions();

    expect($instructions)
        ->toContain('You are Sam Rivera, a developer')
        ->toContain('https://sam.example.com')
        ->toContain(route('contact'))
        ->toContain('First person, always')
        ->toContain("you're Sam's AI assistant")
        ->toContain('Never invent')
        ->toContain('Politely decline')
        ->toContain('SaveLead');
});

it('reads the provider, model and timeout from config', function () {
    config()->set('portfolio.chat.provider', 'openai');
    config()->set('portfolio.chat.model', 'small-model');
    config()->set('portfolio.chat.timeout', 15);

    $agent = new PortfolioAgent;

    expect($agent->provider())->toBe('openai')
        ->and($agent->model())->toBe('small-model')
        ->and($agent->timeout())->toBe(15);
});

it('gives the model every portfolio tool', function () {
    $toolClasses = collect((new PortfolioAgent)->tools())->map(fn (object $tool): string => $tool::class)->all();

    expect($toolClasses)->toBe([
        GetAbout::class,
        GetProjects::class,
        GetProjectDetails::class,
        GetSkills::class,
        GetExperience::class,
        GetBlogPosts::class,
        GetContactInfo::class,
        SaveLead::class,
    ]);
});

it('has no history without a conversation', function () {
    expect((new PortfolioAgent)->messages())->toBe([]);
});

it('replays the conversation history oldest first', function () {
    $conversation = ChatConversation::factory()->create();
    ChatMessage::factory()->for($conversation, 'conversation')->create(['content' => 'What have you built?']);
    ChatMessage::factory()->fromAssistant()->for($conversation, 'conversation')->create(['content' => 'Sam built Helpdesk Copilot.']);

    $messages = (new PortfolioAgent($conversation))->messages();

    expect($messages)->toHaveCount(2)
        ->and($messages[0]->role)->toBe(MessageRole::User)
        ->and($messages[0]->content)->toBe('What have you built?')
        ->and($messages[1]->role)->toBe(MessageRole::Assistant);
});

it('only replays the most recent turns', function () {
    config()->set('portfolio.chat.max_turns', 2);

    $conversation = ChatConversation::factory()->create();
    foreach (range(1, 6) as $number) {
        ChatMessage::factory()->for($conversation, 'conversation')->create(['content' => "Message {$number}"]);
    }

    $contents = array_map(fn ($message) => $message->content, (new PortfolioAgent($conversation))->messages());

    expect($contents)->toBe(['Message 3', 'Message 4', 'Message 5', 'Message 6']);
});

it('answers visitor questions through the faked provider', function () {
    PortfolioAgent::fake(['Sam has built several Laravel apps.'])->preventStrayPrompts();

    $response = (new PortfolioAgent)->prompt('What have you built?');

    expect($response->text)->toBe('Sam has built several Laravel apps.');

    PortfolioAgent::assertPrompted(fn (AgentPrompt $prompt): bool => $prompt->contains('What have you built?'));
});
