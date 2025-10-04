@extends('layouts.main')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Surat Jalan: {{ $sj->unique_code }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Detail informasi dan tracking</p>
        </div>

        <!-- Info Surat Jalan -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-2">
            <div><span class="font-semibold">Sender:</span> {{ $sj->sender_name }}</div>
            <div><span class="font-semibold">Receiver:</span> {{ $sj->receiver_name }}</div>
            <div>
                <span class="font-semibold">Status:</span>
                <span
                    class="px-2 py-1 rounded text-sm 
                {{ $sj->status === 'delivered' ? 'bg-green-100 text-green-700 dark:bg-green-700 dark:text-white' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-700 dark:text-white' }}">
                    {{ ucfirst($sj->status) }}
                </span>
            </div>
        </div>

        {{-- QR Code hanya tampil jika belum delivered --}}
        @if ($sj->status !== 'delivered')
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-100">QR untuk Update Lokasi</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Scan oleh kurir untuk update posisi.</p>
                <div class="mt-4 flex flex-col items-center">
                    <div class="border p-3 rounded bg-gray-50 dark:bg-gray-700">{!! $qr !!}</div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Link: {{ $url }}</p>
                </div>
            </div>
        @endif

        {{-- Maps --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="font-semibold text-lg mb-3 text-gray-800 dark:text-gray-100">📍 Lokasi Tracking</h3>

            {{-- Container untuk Leaflet map --}}
            <div id="map" class="w-full h-64 rounded shadow"></div>

            <div class="space-y-4 mt-4">
                @php
                    $last = $tracking->last(); // lokasi terakhir
                @endphp

                {{-- Lokasi terakhir tampil koordinat --}}
                @if ($last)
                    <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                        Lokasi terakhir: {{ $last->lat }}, {{ $last->lng }}
                    </p>
                @endif

                {{-- Lokasi sebelumnya hanya koordinat --}}
                @foreach ($tracking->slice(0, -1) as $item)
                    <div class="p-2 border rounded bg-gray-50 dark:bg-gray-700">
                        <p class="text-sm text-gray-700 dark:text-gray-200">
                            Koordinat: {{ $item->lat }}, {{ $item->lng }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>



        {{-- Jika sudah delivered, tampilkan detail penerimaan --}}
        @if ($sj->status === 'delivered' && $sj->proofs->count())
            <div class="bg-green-50 dark:bg-green-800 p-6 rounded-lg shadow space-y-4">
                <h2 class="font-semibold text-lg text-green-800 dark:text-green-100">📦 Paket Telah Diterima</h2>
                @foreach ($sj->proofs as $proof)
                    <div class="space-y-2">
                        <p><strong>Nama Penerima:</strong> {{ $proof->recipient_name }}</p>
                        <p><strong>Waktu Terima:</strong>
                            {{ \Carbon\Carbon::parse($proof->received_at)->format('d M Y H:i') }}</p>
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $proof->photo_path) }}" alt="Bukti Foto"
                                class="w-full max-w-md rounded-lg shadow">
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Form upload bukti hanya untuk kurir & kalau belum delivered --}}
        @if (auth()->check() && auth()->user()->isKurir() && $sj->status !== 'delivered')
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg mb-4 text-gray-800 dark:text-gray-100">Upload Bukti Serah Terima</h3>
                <form method="POST" action="{{ route('surat.upload.proof', $sj->id) }}" enctype="multipart/form-data"
                    class="space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-1">Nama Penerima</label>
                        <input type="text" name="recipient_name" required
                            class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block mb-1">Waktu Terima</label>
                        <input type="datetime-local" name="received_at" required
                            class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div>
                        <label class="block mb-1">Foto Bukti</label>
                        <input type="file" name="photo" accept="image/*" required
                            class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            Upload Bukti
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>

    {{-- Leaflet Maps --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
        const id = {{ $sj->id }};
        const map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker; // biar marker lama bisa diganti

        async function loadLatest() {
            try {
                const res = await fetch('/surat/' + id + '/locations/latest');
                const data = await res.json();
                const latest = data.latest;

                if (latest) {
                    const latlng = [latest.lat, latest.lng];

                    // pindah peta ke lokasi terbaru
                    map.setView(latlng, 15);

                    // hapus marker lama kalau ada
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    // pasang marker baru
                    marker = L.marker(latlng).addTo(map)
                        .bindPopup("Lokasi terbaru<br>" + latest.created_at)
                        .openPopup();
                }
            } catch (e) {
                console.error("Gagal load lokasi:", e);
            }
        }

        loadLatest();
        setInterval(loadLatest, 30000); // auto-refresh tiap 30 detik
    </script>
@endsection
