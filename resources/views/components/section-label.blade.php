@props(['number', 'label'])

<p {{ $attributes->merge(['class' => 'section-label']) }}>{{ $number }} — {{ $label }}</p>
