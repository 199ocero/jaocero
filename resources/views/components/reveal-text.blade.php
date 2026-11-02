{{--
    Reveals text word by word. Phrases wrapped in *asterisks* get the marker highlight,
    which draws in right after its words appear. Long phrases get a marker that wraps with the text.

    Built as one string so no stray whitespace lands inside a marker: a wrapping marker would
    paint it as a blue sliver at the end of a line.
--}}
@props(['text', 'delay' => 0, 'step' => 70])

@php
    $wordIndex = 0;
    $segmentsHtml = [];

    foreach (\App\Support\Highlight::segments($text) as $segment) {
        $words = preg_split('/\s+/', $segment['text']) ?: [];

        $wordsHtml = implode(' ', array_map(
            fn (string $word): string => '<span class="inline-block motion-safe:animate-word" style="animation-delay: '.($delay + $wordIndex++ * $step).'ms">'.e($word).'</span>',
            $words,
        ));

        if ($segment['highlighted']) {
            $isLongPhrase = count($words) > 2 || mb_strlen($segment['text']) > 14;
            $markerClass = $isLongPhrase ? 'marker-animated marker-wrap' : 'marker-animated marker';
            $wordsHtml = '<span class="'.$markerClass.'" style="--marker-delay: '.($delay + $wordIndex * $step).'ms">'.$wordsHtml.'</span>';
        }

        $segmentsHtml[] = $wordsHtml;
    }
@endphp

{!! implode(' ', $segmentsHtml) !!}
