@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<div class="min-h-screen bg-stone-100 dark:bg-gray-950 py-10 px-4">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">User Management</h1>
                <p class="text-sm text-stone-400 dark:text-gray-500 mt-1">Administration — Manage user permissions</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="px-4 py-1.5 text-sm font-semibold border border-stone-300 dark:border-gray-700 rounded-full text-stone-600 dark:text-gray-400 hover:bg-gray-900 hover:text-white dark:hover:bg-gray-100 dark:hover:text-gray-900 transition-all duration-200">
                ← Dashboard
            </a>
        </div>

        {{-- Summary --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $users->count() }}</div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Total Users</div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-green-500">{{ $users->where('can_book_vehicle', true)->count() }}</div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Can Book Vehicle</div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 p-4 text-center">
                <div class="text-3xl font-bold text-blue-500">{{ $users->where('is_admin', true)->count() }}</div>
                <div class="text-xs text-stone-400 dark:text-gray-500 mt-1 uppercase tracking-widest">Admins</div>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input type="text" id="userSearch" placeholder="Search users by name or email..."
                oninput="filterUsers()"
                class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-gray-100" />
        </div>

        {{-- Users Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow border border-stone-200 dark:border-gray-800 overflow-hidden">
            <table class="w-full text-sm" id="usersTable">
                <thead>
                    <tr class="bg-gray-900 dark:bg-gray-950 text-stone-300 text-xs uppercase tracking-widest">
                        <th class="px-5 py-3 text-left">Name</th>
                        <th class="px-5 py-3 text-left">Email</th>
                        <th class="px-5 py-3 text-center">Admin</th>
                        <th class="px-5 py-3 text-center">Can Book Vehicle</th>
                        <th class="px-5 py-3 text-center">Vehicle Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 dark:divide-gray-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-stone-50 dark:hover:bg-gray-800 transition user-row"
                        data-name="{{ strtolower($user->name) }}"
                        data-email="{{ strtolower($user->email) }}">
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                        </td>
                        <td class="px-5 py-3 text-stone-500 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($user->is_admin)
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-semibold">Admin</span>
                            @else
                                <span class="text-stone-300 dark:text-gray-700 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button
                                onclick="toggleVehicle({{ $user->id }}, this)"
                                data-enabled="{{ $user->can_book_vehicle ? '1' : '0' }}"
                                class="relative inline-flex items-center w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none
                                    {{ $user->can_book_vehicle ? 'bg-green-500' : 'bg-stone-300 dark:bg-gray-700' }}">
                                <span class="inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200
                                    {{ $user->can_book_vehicle ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button
                                onclick="toggleVehicleAdmin({{ $user->id }}, this)"
                                data-enabled="{{ $user->is_vehicle_admin ? '1' : '0' }}"
                                class="relative inline-flex items-center w-11 h-6 rounded-full transition-colors duration-200 focus:outline-none
                                    {{ $user->is_vehicle_admin ? 'bg-purple-500' : 'bg-stone-300 dark:bg-gray-700' }}">
                                <span class="inline-block w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-200
                                    {{ $user->is_vehicle_admin ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="text-center text-xs text-stone-400 dark:text-gray-600 mt-6">
            Crestec Philippines, Inc. — User Management
        </p>

    </div>
</div>

<script>
    function filterUsers() {
        const q     = document.getElementById('userSearch').value.toLowerCase();
        const rows  = document.querySelectorAll('.user-row');
        rows.forEach(row => {
            const name  = row.dataset.name;
            const email = row.dataset.email;
            row.style.display = (name.includes(q) || email.includes(q)) ? '' : 'none';
        });
    }

    async function toggleVehicleAdmin(userId, btn) {
        btn.disabled = true;
        try {
            const res  = await fetch(`/admin/users/${userId}/toggle-vehicle-admin`, {
                method:  'PATCH',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
            });
            const data = await res.json();
            if (!res.ok) { alert('Failed to update permission.'); return; }

            const enabled = data.is_vehicle_admin;
            btn.dataset.enabled = enabled ? '1' : '0';
            btn.className = btn.className.replace('bg-purple-500','').replace('bg-stone-300','').replace('dark:bg-gray-700','').trim();
            btn.classList.add(...(enabled ? ['bg-purple-500'] : ['bg-stone-300','dark:bg-gray-700']));
            const knob = btn.querySelector('span');
            knob.className = knob.className.replace('translate-x-6','').replace('translate-x-1','').trim();
            knob.classList.add(enabled ? 'translate-x-6' : 'translate-x-1');
        } catch (e) { alert('Network error.'); }
        finally { btn.disabled = false; }
    }
        btn.disabled = true;

        try {
            const res  = await fetch(`/admin/users/${userId}/toggle-vehicle`, {
                method:  'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
            });
            const data = await res.json();

            if (!res.ok) { alert('Failed to update permission.'); return; }

            const enabled = data.can_book_vehicle;
            btn.dataset.enabled = enabled ? '1' : '0';

            // Update button color
            btn.className = btn.className
                .replace('bg-green-500', '')
                .replace('bg-stone-300', '')
                .replace('dark:bg-gray-700', '')
                .trim();
            btn.classList.add(...(enabled
                ? ['bg-green-500']
                : ['bg-stone-300', 'dark:bg-gray-700']
            ));

            // Update knob position
            const knob = btn.querySelector('span');
            knob.className = knob.className
                .replace('translate-x-6', '')
                .replace('translate-x-1', '')
                .trim();
            knob.classList.add(enabled ? 'translate-x-6' : 'translate-x-1');

        } catch (e) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
        }
    }
</script>

@endsection
