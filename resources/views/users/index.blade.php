<x-layout>
    <x-slot name="header">
        Users Page
    </x-slot>

    <div class="space-y-4">
        @if(session('success') || session('error'))
            @if(session('success'))
                <div class="bg-green-500 text-white p-4 rounded-md">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="bg-red-500 text-white p-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
        @endif

        <div class="bg-white p-6 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="mx-auto pb-4">
                <a href="/users/create"
                    class="bg-green-600 hover:bg-green-500 rounded-md px-3 py-2 text-sm font-semibold text-white shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Create User
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white shadow-md rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Name</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Email</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Role</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-900 uppercase tracking-wider text-left">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->role }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="/users/{{ $user->id }}/edit" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="/users/{{ $user->id }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
            </div>
        </div>
    </div>
</x-layout>