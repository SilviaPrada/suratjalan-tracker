@extends('layouts.main')

@section('content')
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <!-- Alert Success -->
        @if (session('success'))
            <div
                class="mb-4 p-3 rounded bg-green-100 text-green-800 
                dark:bg-green-800 dark:text-green-100 
                flex items-center gap-2">
                <x-heroicon-o-check class="w-5 h-5 flex-shrink-0" />
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="mt-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 dark:text-gray-100">
                    Daftar Surat Jalan
                </h2>

                <div class="flex justify-start sm:justify-end">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('surat.create') }}"
                            class="bg-blue-600 text-white px-3 py-2 sm:px-4 sm:py-2 rounded-lg hover:bg-blue-700 text-sm sm:text-base text-center">
                            + Buat Surat Jalan
                        </a>
                    @endif
                </div>
            </div>

            <!-- Versi Tabel (≥ sm) -->
            <div class="hidden sm:block overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg">
                <table class="min-w-full border-collapse text-sm sm:text-base">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700 text-left text-gray-700 dark:text-gray-200">
                            <th class="px-4 py-2">Kode</th>
                            <th class="px-4 py-2">Sender</th>
                            <th class="px-4 py-2">Receiver</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Lokasi</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $sj)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-2">{{ $sj->unique_code }}</td>
                                <td class="px-4 py-2">{{ $sj->sender_name }}</td>
                                <td class="px-4 py-2">{{ $sj->receiver_name }}</td>
                                <td class="px-4 py-2">
                                    <span
                                        class="px-2 py-1 rounded text-white 
                                        {{ $sj->status == 'delivered' ? 'bg-green-600' : ($sj->status == 'in_transit' ? 'bg-yellow-600' : 'bg-gray-600') }}">
                                        {{ ucfirst($sj->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    @if ($sj->current_lat && $sj->current_lng)
                                        {{ $sj->current_lat }}, {{ $sj->current_lng }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <x-button.detail href="{{ route('surat.show', $sj->id) }}">
                                        Detail
                                    </x-button.detail>

                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('surat.destroy', $sj->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus surat jalan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-button.delete href="{{ route('surat.show', $sj->id) }}">
                                                Hapus
                                            </x-button.delete>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center p-4">Belum ada surat jalan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Versi Card (< sm) -->
            <div class="space-y-4 sm:hidden">
                @forelse($list as $sj)
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $sj->unique_code }}</span>
                            <span
                                class="px-2 py-1 text-xs rounded text-white 
                                {{ $sj->status == 'delivered' ? 'bg-green-600' : ($sj->status == 'in_transit' ? 'bg-yellow-600' : 'bg-gray-600') }}">
                                {{ ucfirst($sj->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">Sender: {{ $sj->sender_name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">Receiver: {{ $sj->receiver_name }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Lokasi: {{ $sj->current_lat ? $sj->current_lat . ', ' . $sj->current_lng : '-' }}
                        </div>
                        <div class="mt-3">
                            <x-button.detail href="{{ route('surat.show', $sj->id) }}">
                                Detail
                            </x-button.detail>
                        </div>
                    </div>
                @empty
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow text-center">
                        Belum ada surat jalan.
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $list->links() }}
            </div>
        </div>
    </div>
@endsection
