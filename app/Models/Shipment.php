<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'organization_id', 'user_id', 'status',
        
        // References
        'agent_reference', 'shippers_reference', 'mbl',
        
        // Parties
        'shipper_name', 'shipper_address1', 'shipper_address2', 'shipper_city', 'shipper_state', 'shipper_postcode', 'shipper_country',
        'consignee_name', 'consignee_address1', 'consignee_address2', 'consignee_city', 'consignee_state', 'consignee_postcode', 'consignee_country',
        'pinnacle_office',
        
        // Transport
        'mode', 'incoterms',
        'carrier_name', 'vessel', 'voyage', 
        'origin_port', 'destination_port', 
        'etd', 'eta',
        
        // Cargo
        'goods_description', // Commodity
        'qty_value', 'qty_type', 
        'weight_value', 'volume_qty', 
        'container_number', 'container_type', 'seal_number'
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
public function organization() {
    return $this->belongsTo(Organization::class);
}
public function shipper()
    {
        return $this->belongsTo(\App\Models\Shipper::class, 'shipper_id');
    }
}