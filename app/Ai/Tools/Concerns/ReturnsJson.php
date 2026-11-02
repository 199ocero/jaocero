<?php

namespace App\Ai\Tools\Concerns;

trait ReturnsJson
{
    /**
     * Encode tool output as compact, readable JSON for the model.
     *
     * @param  array<array-key, mixed>  $data
     */
    protected function json(array $data): string
    {
        return (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
