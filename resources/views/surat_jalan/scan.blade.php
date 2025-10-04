@extends('layouts.main')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
            📍 Scan Lokasi - {{ $sj->unique_code }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Sender: <span class="font-medium">{{ $sj->sender_name }}</span> |
            Receiver: <span class="font-medium">{{ $sj->receiver_name }}</span>
        </p>
    </div>

    <!-- Card Aksi -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow text-center">
        <p class="text-gray-700 dark:text-gray-300 mb-4">
            Klik tombol di bawah untuk mengirim lokasi terkini.
        </p>

        <!-- Tombol dengan loading -->
        <button id="btnLocate"
            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-md font-medium w-full sm:w-auto">
            <svg id="loadingIcon" class="animate-spin h-5 w-5 text-white hidden"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span id="btnText">Kirim Lokasi Saat Ini</span>
        </button>

        <!-- Status -->
        <div id="status" class="mt-4 text-sm text-gray-600 dark:text-gray-400"></div>
    </div>
</div>

<script>
    const btnLocate = document.getElementById('btnLocate');
    const statusEl = document.getElementById('status');
    const btnText = document.getElementById('btnText');
    const loadingIcon = document.getElementById('loadingIcon');

    function setLoading(isLoading, message = '') {
        if (isLoading) {
            btnLocate.disabled = true;
            loadingIcon.classList.remove('hidden');
            btnText.textContent = 'Memproses...';
            if (message) statusEl.textContent = message;
        } else {
            btnLocate.disabled = false;
            loadingIcon.classList.add('hidden');
            btnText.textContent = 'Kirim Lokasi Saat Ini';
        }
    }

    btnLocate.addEventListener('click', async () => {
        if (!navigator.geolocation) {
            statusEl.textContent = '❌ Geolocation tidak tersedia di browser ini.';
            return;
        }

        setLoading(true, '📡 Mengambil lokasi...');
        navigator.geolocation.getCurrentPosition(async (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            setLoading(true, '🚀 Mengirim lokasi...');

            const payload = {
                code: "{{ $sj->unique_code }}",
                lat: lat,
                lng: lng,
                device: navigator.userAgent,
                note: 'update via QR scan page'
            };

            try {
                const res = await fetch('{{ route('surat.update.location') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });
                if (res.ok) {
                    statusEl.textContent = '✅ Lokasi berhasil dikirim.';
                } else {
                    const j = await res.json();
                    statusEl.textContent = '❌ Error: ' + JSON.stringify(j);
                }
            } catch (e) {
                statusEl.textContent = '❌ Gagal kirim lokasi: ' + e.message;
            } finally {
                setLoading(false);
            }

        }, (err) => {
            setLoading(false);
            statusEl.textContent = '❌ Gagal ambil lokasi: ' + err.message;
        });
    });
</script>
@endsection
