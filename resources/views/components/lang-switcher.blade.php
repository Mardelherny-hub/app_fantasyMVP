@php
  $current = app()->getLocale();
  $segments = request()->segments();
  if (in_array($segments[0] ?? '', ['es','en','fr'])) array_shift($segments);
  $path = implode('/', $segments);
@endphp

<div class="flex gap-2">
  @foreach (['es' => 'ES', 'en' => 'EN', 'fr' => 'FR'] as $code => $label)
    <a href="{{ url($code.'/'.$path) }}"
       class="px-2 py-1 rounded border {{ $current === $code ? 'bg-gray-200' : '' }}">
       {{ $label }}
    </a>
  @endforeach
</div>
