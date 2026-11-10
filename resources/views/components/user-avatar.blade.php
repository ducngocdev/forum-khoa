@props(['user', 'size' => 'w-8 h-8', 'textSize' => 'text-xs'])

@php
    $avatarUrl = $user?->avatar_path ? asset('storage/' . ltrim($user->avatar_path, '/')) : null;
    $initial = mb_substr($user?->name ?? 'U', 0, 1);
    $role = data_get($user, 'role');
    $academicTitle = mb_strtolower(trim((string) data_get($user, 'academic_title')));
    $frameClass = match (true) {
        $role === 'admin' => 'user-avatar--admin',
        $role === 'moderator' => 'user-avatar--moderator',
        in_array($academicTitle, ['giảng viên', 'giáo viên'], true) => 'user-avatar--educator',
        in_array($academicTitle, ['đã tốt nghiệp', 'cựu sinh viên'], true) => 'user-avatar--graduate',
        $academicTitle === 'sinh viên' || $role === 'student' => 'user-avatar--student',
        default => 'user-avatar--member',
    };
@endphp

<span {{ $attributes->merge(['class' => 'user-avatar relative inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-600 font-bold uppercase text-white ' . $frameClass . ' ' . $size . ' ' . $textSize]) }}>
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" alt="Ảnh đại diện của {{ $user->name }}" class="absolute inset-0 h-full w-full rounded-full border border-gray-200 object-cover" onerror="this.remove()">
    @endif
    <span aria-hidden="true">{{ $initial }}</span>
</span>
