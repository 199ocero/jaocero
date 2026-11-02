<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MessageSource: string implements HasLabel
{
    case ContactForm = 'contact_form';
    case Chatbot = 'chatbot';

    public function label(): string
    {
        return match ($this) {
            self::ContactForm => 'Contact form',
            self::Chatbot => 'Chatbot',
        };
    }

    public function getLabel(): string
    {
        return $this->label();
    }
}
