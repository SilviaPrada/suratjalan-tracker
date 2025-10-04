<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $fillable = [
        'unique_code','sender_name','receiver_name','description',
        'status','origin_lat','origin_lng','current_lat','current_lng','last_update_at'
    ];

    public function locations()
    {
        return $this->hasMany(DeliveryLocation::class);
    }

    public function proofs()
    {
        return $this->hasMany(DeliveryProof::class);
    }
}
