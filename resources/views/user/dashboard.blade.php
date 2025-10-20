<x-app-layout>
    <x-slot name="header">
        <h2>User Dashboard</h2>
    </x-slot>

    @php
        $roleVal = method_exists($user, 'roleString')
            ? $user->roleString()
            : ($user->role instanceof \App\Enums\Role ? $user->role->value : (string) $user->role);
    @endphp

    <p>Halo, {{ $user->name }} — Role: <strong>{{ $roleVal }}</strong></p>
    <p>Ini halaman role: <strong>user</strong></p>

    @if (session('status'))
        <div>{{ session('status') }}</div>
    @endif

</x-app-layout>
