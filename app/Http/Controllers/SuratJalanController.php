<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\DeliveryLocation;
use App\Models\DeliveryProof;
use Illuminate\Http\Request;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Validator;

class SuratJalanController extends Controller
{
    public function index()
    {
        $list = SuratJalan::orderBy('created_at','desc')->paginate(10);
        return view('surat_jalan.index', compact('list'));
    }

    // tampilkan form create
    public function create()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only admin can create surat jalan');
        }
        return view('surat_jalan.create');
    }

    // simpan surat jalan (auto generate code)
    public function store(Request $request)
    {
        $data = $request->validate([
            'sender_name'=>'nullable|string',
            'receiver_name'=>'nullable|string',
            'description'=>'nullable|string',
            'origin_lat'=>'nullable|numeric',
            'origin_lng'=>'nullable|numeric',
        ]);

        $unique = 'SJ-' . now()->format('YmdHis') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(5));
        $data['unique_code'] = $unique;

        $sj = \App\Models\SuratJalan::create($data);

        // jika ada lokasi awal
        if ($request->filled('origin_lat') && $request->filled('origin_lng')) {
            $sj->locations()->create([
                'lat'=>$request->origin_lat,
                'lng'=>$request->origin_lng,
                'device'=>'system',
                'note'=>'origin'
            ]);
            $sj->update([
                'origin_lat'=>$request->origin_lat,
                'origin_lng'=>$request->origin_lng,
                'current_lat'=>$request->origin_lat,
                'current_lng'=>$request->origin_lng,
                'last_update_at'=>now(),
            ]);
        }

        // 🔹 setelah buat surat jalan → kembali ke list
        return redirect()->route('surat.index')->with('success','Surat Jalan berhasil dibuat.');
    }


    // tampilkan detail + QR + peta lokasi terkini
    public function show($id)
    {
        $sj = SuratJalan::with('locations','proofs')->findOrFail($id);

        // generate QR (SVG string) untuk ditampilkan in-line
        $url = route('surat.scan.code', ['code'=>$sj->unique_code]);
        $qr = QrCode::size(300)->generate($url);

        $tracking = $sj->locations; 

        return view('surat_jalan.show', compact('sj','qr','url','tracking'));
    }

    // endpoint scan QR (akses via link di QR) -> menampilkan halaman scan / update
    public function scanByCode($code)
    {
        if (!auth()->check() || !auth()->user()->isKurir()) {
            abort(403, 'Access denied, only courier can update location');
        }

        $sj = SuratJalan::where('unique_code', $code)->firstOrFail();
        return view('surat_jalan.scan', compact('sj'));
    }

    // POST: update lokasi (dipanggil setelah scan / oleh device)
    public function updateLocation(Request $request)
    {
        if (!auth()->check() || !auth()->user()->isKurir()) {
            abort(403, 'Access Denied, Kurir Only can Update Location');
        }

        $v = Validator::make($request->all(), [
            'code'=>'required|string|exists:surat_jalans,unique_code',
            'lat'=>'required|numeric',
            'lng'=>'required|numeric',
            'device'=>'nullable|string',
            'note'=>'nullable|string'
        ]);

        if ($v->fails()) {
            return response()->json(['errors'=>$v->errors()], 422);
        }

        $sj = SuratJalan::where('unique_code', $request->code)->firstOrFail();

        $loc = $sj->locations()->create([
            'lat'=>$request->lat,
            'lng'=>$request->lng,
            'device'=>$request->device ?? 'unknown',
            'note'=>$request->note ?? null
        ]);

        $sj->update([
            'current_lat'=>$request->lat,
            'current_lng'=>$request->lng,
            'last_update_at'=>now(),
            'status'=>'in_transit'
        ]);

        return response()->json(['ok'=>true,'location'=>$loc]);
    }


    // upload proof of delivery
    public function uploadProof(Request $request, $id)
    {
        if (!auth()->check() || !auth()->user()->isKurir()) {
            abort(403, 'Access Denied, Kurir Only can Upload Proof');
        }

        $sj = SuratJalan::findOrFail($id);

        $v = $request->validate([
            'recipient_name'=>'required|string',
            'photo'=>'required|image|max:5120',
            'received_at'=>'required|date'
        ]);

        $path = $request->file('photo')->store('delivery_proofs','public');

        $sj->proofs()->create([
            'recipient_name'=>$v['recipient_name'],
            'photo_path'=>$path,
            'received_at'=>Carbon::parse($v['received_at'])
        ]);

        $sj->update([
            'status'=>'delivered',
            'last_update_at'=>now(),
        ]);

        return redirect()->route('surat.show', $sj->id)->with('success','Bukti serah terima diupload.');
    }

    // API to fetch latest location (for map)
    public function latestLocation($id)
    {
        $sj = SuratJalan::with('locations')->findOrFail($id);
        $latest = $sj->locations()->orderBy('created_at','desc')->first();
        return response()->json(['latest'=>$latest, 'all'=>$sj->locations]);
    }

    public function destroy($id)
    {
        $sj = SuratJalan::findOrFail($id);

        // Hapus relasi terkait (lokasi & bukti jika ada)
        $sj->locations()->delete();
        $sj->proofs()->delete();

        $sj->delete();

        return redirect()->route('surat.index')->with('success', 'Surat Jalan berhasil dihapus.');
    }

}
