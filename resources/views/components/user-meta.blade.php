@props(['user', 'time' => null, 'action' => null])

@php
    $details = collect([$user?->academic_title, $user?->study_year, $user?->class_name])->filter()->join(' · ');
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2 text-xs text-gray-500 flex-wrap']) }}>
    <x-user-avatar :user="$user" size="w-7 h-7" text-size="text-[11px]" />
    @if($user)
        <a href="{{ route('users.show', $user) }}" class="font-semibold text-gray-800 hover:text-blue-600">
            {{ $user->name }}
        </a>
        <x-user-role-badge :user="$user" />
        @if($details)
            <span class="text-gray-500">{{ $details }}</span>
        @endif
        <span class="text-gray-400">({{ $user->display_reputation ?? 0 }} điểm)</span>
    @endif
    @if($action)
        <span class="text-gray-400">·</span><span>{{ $action }}</span>
    @endif
    @if($time)
        <span class="text-gray-400">·</span><span>{{ $time }}</span>
    @endif
</div>
