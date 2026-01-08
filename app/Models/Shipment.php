<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
    'agent_id', 'user_id', 'agent_reference', 'shippers_reference', 'mbl',
    'shipper_name', 'consignee_name', 'pinnacle_office', 'mode', 'incoterms',
    'vessel', 'voyage', 'origin_port', 'destination_port', 'etd', 'eta',
    'qty_value', 'qty_type', 'weight_value', 'volume_qty', 'container_number', 'status'
   ];
   
   protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];
	
   public function user() {
    return $this->belongsTo(User::class);
}

}
