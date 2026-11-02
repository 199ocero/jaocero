<?php

namespace App\Livewire\Pages;

use App\Enums\MessageSource;
use App\Models\About;
use App\Models\Message;
use App\Support\Seo\SeoData;
use App\Support\Seo\StructuredData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class Contact extends Component
{
    public string $name = '';

    public string $email = '';

    public string $company = '';

    public string $message = '';

    /**
     * Honeypot field. Hidden from people, so only bots fill it in.
     */
    public string $website = '';

    public bool $sent = false;

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function send(): void
    {
        $validated = $this->validate();

        if ($this->website !== '') {
            $this->sent = true;

            return;
        }

        $rateLimitKey = 'contact:'.request()->ip();
        $maxAttempts = (int) config('portfolio.contact.rate_limit.max_attempts');

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $minutes = (int) ceil(RateLimiter::availableIn($rateLimitKey) / 60);

            $this->addError('form', "You've sent a few messages already. Please try again in {$minutes} ".str('minute')->plural($minutes).'.');

            return;
        }

        RateLimiter::hit($rateLimitKey, (int) config('portfolio.contact.rate_limit.decay_minutes') * 60);

        Message::create([
            ...$validated,
            'company' => $validated['company'] ?: null,
            'source' => MessageSource::ContactForm,
        ]);

        $this->reset('name', 'email', 'company', 'message');
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.pages.contact', [
            'about' => About::current(),
        ])->layoutData([
            'seo' => new SeoData(
                title: 'Contact',
                description: 'Get in touch with '.config('portfolio.owner_name').' about work, projects or collaborations.',
                canonicalUrl: route('contact'),
                jsonLd: [StructuredData::webPage('ContactPage', 'Contact', route('contact'))],
            ),
        ]);
    }
}
