@extends('layouts.main')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-2xl font-semibold mb-6">Buat Surat Jalan</h2>

    <form id="formSurat" action="{{ route('surat.store') }}" method="POST" 
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        @csrf

        <div>
            <label class="block mb-1">Sender Name</label>
            <input type="text" name="sender_name" value="{{ old('sender_name') }}"
                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600" required>
        </div>

        <div>
            <label class="block mb-1">Receiver Name</label>
            <input type="text" name="receiver_name" value="{{ old('receiver_name') }}"
                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600" required>
        </div>

        <div>
            <label class="block mb-1">Description</label>
            <textarea name="description" 
                      class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600">{{ old('description') }}</textarea>
        </div>

        <!-- Hidden input untuk auto isi lokasi -->
        <input type="hidden" name="origin_lat" id="origin_lat">
        <input type="hidden" name="origin_lng" id="origin_lng">

        <!-- Status -->
        <div id="status" class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mt-2">
            <div id="loadingSpinner" class="hidden animate-spin rounded-full h-4 w-4 border-2 border-blue-500 border-t-transparent"></div>
            <span id="statusText"></span>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('surat.index') }}" 
               class="px-4 py-2 border rounded-md dark:border-gray-600">
                Batal
            </a>
            <button id="btnSubmit" type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                disabled>
                Create
            </button>
        </div>
    </form>
</div>

<script>
    const statusText = document.getElementById('statusText');
    const spinner = document.getElementById('loadingSpinner');
    const btn = document.getElementById('btnSubmit');
    const latInput = document.getElementById('origin_lat');
    const lngInput = document.getElementById('origin_lng');

    function setLoading(message) {
        spinner.classList.remove('hidden');
        statusText.textContent = message;
    }

    function setStatus(message, isError = false) {
        spinner.classList.add('hidden');
        statusText.textContent = message;
        statusText.className = isError 
            ? "text-sm text-red-600 dark:text-red-400 mt-2"
            : "text-sm text-green-600 dark:text-green-400 mt-2";
    }

    // ambil lokasi saat load
    if (navigator.geolocation) {
        setLoading("Mengambil lokasi Anda...");
        navigator.geolocation.getCurrentPosition(function(pos) {
            latInput.value = pos.coords.latitude;
            lngInput.value = pos.coords.longitude;
            setStatus("✅ Lokasi berhasil didapat. Anda bisa membuat surat jalan.");
            btn.disabled = false;
        }, function(err) {
            setStatus("⚠️ Gagal mengambil lokasi: " + err.message, true);
            btn.disabled = false; // tetap bisa submit walau tanpa lokasi
        });
    } else {
        setStatus("Geolocation tidak didukung browser ini.", true);
        btn.disabled = false;
    }
</script>
@endsection
