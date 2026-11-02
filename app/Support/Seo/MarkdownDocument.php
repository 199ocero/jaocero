<?php

namespace App\Support\Seo;

/**
 * A tiny builder for markdown documents, so every presenter formats content the same way.
 */
class MarkdownDocument
{
    /**
     * @var list<string>
     */
    private array $blocks = [];

    public static function make(): self
    {
        return new self;
    }

    /**
     * A markdown link with an optional note, e.g. "[Title](https://...): One-line summary".
     */
    public static function link(string $label, string $url, ?string $note = null): string
    {
        $link = '['.addcslashes($label, '[]').']('.$url.')';

        return $note === null || trim($note) === '' ? $link : $link.': '.trim($note);
    }

    public function heading(string $text, int $level = 1): self
    {
        return $this->add(str_repeat('#', $level).' '.$text);
    }

    public function quote(?string $text): self
    {
        return $this->add($text === null || trim($text) === '' ? null : '> '.trim($text));
    }

    /**
     * A bullet list of "Label: value" pairs.
     *
     * @param  array<string, string>  $facts
     */
    public function facts(array $facts): self
    {
        $lines = array_map(
            fn (string $label, string $value): string => "- {$label}: {$value}",
            array_keys($facts),
            $facts,
        );

        return $this->add($lines === [] ? null : implode("\n", $lines));
    }

    /**
     * A bullet list. Empty lists are skipped.
     *
     * @param  iterable<string>  $items
     */
    public function list(iterable $items): self
    {
        $lines = [];

        foreach ($items as $item) {
            $lines[] = '- '.$item;
        }

        return $this->add($lines === [] ? null : implode("\n", $lines));
    }

    /**
     * A heading followed by its content, both skipped when the content is empty.
     */
    public function section(string $heading, ?string $content, int $level = 2): self
    {
        if ($content === null || trim($content) === '') {
            return $this;
        }

        return $this->heading($heading, $level)->paragraph($content);
    }

    public function paragraph(?string $text): self
    {
        return $this->add($text === null || trim($text) === '' ? null : trim($text));
    }

    public function toString(): string
    {
        return implode("\n\n", $this->blocks)."\n";
    }

    private function add(?string $block): self
    {
        if ($block !== null) {
            $this->blocks[] = $block;
        }

        return $this;
    }
}
