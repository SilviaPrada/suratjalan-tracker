<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryProof extends Model
{
    protected $fillable = ['surat_jalan_id','recipient_name','photo_path','received_at'];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
