<?php

namespace App\Support\Seo;

use App\Models\About;
use App\Models\Experience;
use App\Models\Skill;
use App\Support\Highlight;

/**
 * Question-and-answer pairs about the owner, built from portfolio content.
 *
 * Shown on the About page and in its markdown version: short, direct answers under
 * question headings are the passages AI search engines most readily quote.
 */
class QuickAnswers
{
    /**
     * @return list<array{question: string, answer: string}> Answers are markdown.
     */
    public static function all(): array
    {
        $name = (string) config('portfolio.owner_name');
        $about = About::current();

        return array_values(array_filter([
            self::whatTheyDo($name, $about),
            self::techStack($name),
            self::workHistory($name),
            self::availability($name, $about),
            self::contact($name, $about),
        ]));
    }

    /**
     * @return array{question: string, answer: string}
     */
    private static function whatTheyDo(string $name, About $about): array
    {
        $answer = "{$name} is ".Highlight::plain((string) config('portfolio.headline')).'.';

        if ($about->location) {
            $answer .= " Location: {$about->location}.";
        }

        return [
            'question' => "What does {$name} do?",
            'answer' => $answer." See [{$name}'s projects](".route('projects.index').').',
        ];
    }

    /**
     * @return array{question: string, answer: string}|null
     */
    private static function techStack(string $name): ?array
    {
        $skills = Skill::groupedByCategory()->flatten(1)->take(8)->pluck('name');

        if ($skills->isEmpty()) {
            return null;
        }

        return [
            'question' => "What is {$name}'s tech stack?",
            'answer' => "{$name} mostly works with ".$skills->join(', ', ' and ').'.',
        ];
    }

    /**
     * @return array{question: string, answer: string}|null
     */
    private static function workHistory(string $name): ?array
    {
        $experiences = Experience::query()->latestFirst()->limit(3)->get();
        $latest = $experiences->first();

        if ($latest === null) {
            return null;
        }

        $answer = $latest->isCurrent()
            ? "{$name} currently works as {$latest->role} at {$latest->company}."
            : "{$name} most recently worked as {$latest->role} at {$latest->company}.";

        $previous = $experiences->skip(1)->pluck('company')->unique();

        if ($previous->isNotEmpty()) {
            $answer .= ' Before that: '.$previous->join(', ', ' and ').'.';
        }

        return [
            'question' => "Where has {$name} worked?",
            'answer' => $answer,
        ];
    }

    /**
     * @return array{question: string, answer: string}
     */
    private static function availability(string $name, About $about): array
    {
        return [
            'question' => "Is {$name} available for hire?",
            'answer' => $about->open_to_work
                ? "Yes. {$name} is open to new opportunities. [Send a message](".route('contact').') to talk about your project.'
                : "{$name} isn't actively looking right now, but is happy to hear about interesting projects through the [contact page](".route('contact').').',
        ];
    }

    /**
     * @return array{question: string, answer: string}
     */
    private static function contact(string $name, About $about): array
    {
        $answer = 'Use the [contact form]('.route('contact').')';

        if ($about->email) {
            $answer .= " or email [{$about->email}](mailto:{$about->email})";
        }

        return [
            'question' => "How can I contact {$name}?",
            'answer' => $answer.'. You can also ask the AI assistant on the [home page]('.route('home').').',
        ];
    }
}
