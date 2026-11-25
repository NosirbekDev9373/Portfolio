@props(['variant' => 'desktop'])

@php
    $linkClass = match ($variant) {
        'mobile' => 'rounded-lg px-3 py-2 text-base font-medium text-muted transition hover:bg-white/5 hover:text-foreground',
        'footer' => 'text-sm text-muted transition hover:text-foreground',
        default => 'rounded-full px-3 py-2 text-sm font-medium text-muted transition hover:bg-white/5 hover:text-foreground',
    };

    $items = collect(config('portfolio.nav'))->map(function (array $item): array {
        $href = route($item['route'] ?? 'home');

        if (! empty($item['anchor'])) {
            $href .= $item['anchor'];
        }

        return [
            'label' => __('site.nav.'.$item['key']),
            'href' => $href,
        ];
    });
@endphp

@foreach ($items as $item)
    <a href="{{ $item['href'] }}" class="{{ $linkClass }}">{{ $item['label'] }}</a>
@endforeach
