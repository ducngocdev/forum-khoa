@props(['user'])

@php
    $role = data_get($user, 'role');
    $academicTitleLabel = trim((string) data_get($user, 'academic_title'));
    $academicTitle = mb_strtolower($academicTitleLabel);
    [$label, $badgeClass] = match (true) {
        $role === 'admin' => ['Quản trị', 'user-role-badge--admin'],
        $role === 'moderator' => ['Kiểm duyệt', 'user-role-badge--moderator'],
        in_array($academicTitle, ['giảng viên', 'giáo viên'], true) => [$academicTitleLabel, 'user-role-badge--educator'],
        in_array($academicTitle, ['đã tốt nghiệp', 'cựu sinh viên'], true) => [$academicTitleLabel, 'user-role-badge--graduate'],
        $academicTitle === 'sinh viên' || $role === 'student' => ['Sinh viên', 'user-role-badge--student'],
        filled($academicTitleLabel) => [$academicTitleLabel, 'user-role-badge--member'],
        default => [null, null],
    };
@endphp

@if($label)
    <span {{ $attributes->class(['user-role-badge', $badgeClass]) }}>{{ $label }}</span>
@endif
