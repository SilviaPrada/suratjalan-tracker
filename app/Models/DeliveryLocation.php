<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryLocation extends Model
{
    protected $fillable = ['surat_jalan_id','lat','lng','device','note'];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
