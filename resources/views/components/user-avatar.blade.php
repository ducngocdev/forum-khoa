@props(['user', 'size' => 'w-8 h-8', 'textSize' => 'text-xs'])

@php
    $avatarUrl = $user?->avatar_path ? asset('storage/' . ltrim($user->avatar_path, '/')) : null;
    $initial = mb_substr($user?->name ?? 'U', 0, 1);
@endphp

<span {{ $attributes->merge(['class' => 'relative inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-600 font-bold uppercase text-white ' . $size . ' ' . $textSize]) }}>
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="Ảnh đại diện của {{ $user->name }}" class="absolute inset-0 h-full w-full rounded-full border border-gray-200 object-cover" onerror="this.remove()">
    @endif
    <span aria-hidden="true">{{ $initial }}</span>
</span>
