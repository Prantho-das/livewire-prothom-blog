{{--
  Optimized lazy image with blur-up effect.
  Props:
    $src  : final image URL (required)
    $alt  : alt text (required)
    $cls  : extra CSS classes (optional)
    $width/$height: optional dimensions for aspect ratio
--}}
@props(['src', 'alt', 'cls' => 'w-full h-full object-cover', 'w' => null, 'h' => null])

@if($src)
<img
    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
    data-src="{{ $src }}"
    alt="{{ $alt }}"
    class="{{ $cls }} img-lazy"
    loading="lazy"
    decoding="async"
    @if($w) width="{{ $w }}" @endif
    @if($h) height="{{ $h }}" @endif
>
@else
<div class="{{ $cls }} bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
    <svg class="w-10 h-10 text-gray-400 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0z"/>
    </svg>
</div>
@endif
