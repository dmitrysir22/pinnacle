<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Backpack\CRUD\app\Models\Traits\CrudTrait; 

class Shipment extends Model
{
    use HasFactory,CrudTrait;

    protected $fillable = [
        'organization_id', 'user_id', 'status',
        
        // XML / EDI Headers
        'sender_id', 'action_code',

        // References
        'agent_reference', 'shippers_reference', 'mbl', 'pinnacle_office',
        
        // Parties (Shipper)
        'shipper_name', 'shipper_address1', 'shipper_address2', 
        'shipper_city', 'shipper_state', 'shipper_postcode', 'shipper_country',
        
        // Parties (Consignee)
        'consignee_name', 'consignee_address1', 'consignee_address2', 
        'consignee_city', 'consignee_state', 'consignee_postcode', 'consignee_country',
        
        // Transport & Routing
        'mode', 'incoterms', 'release_type',
        'carrier_name', 'vessel', 'voyage', 
        'origin_port', 'destination_port', 'etd', 'eta',
        
        // Mother Vessel Details (Phase 1 XML)
        'mother_carrier', 'mother_vessel', 'mother_voyage',
        'mother_origin_port', 'mother_destination_port',
        'mother_etd', 'mother_eta',
        'epu', 'feeder_eta',
        
        // General Description
        'goods_description', 'marks_and_numbers', 
        
        // JSON Arrays (Repeatable Fields)
        'packing_items', // Хранит массив товаров
        'containers',    // Хранит массив контейнеров
        'notes',         // Хранит массив заметок
        
        // Legacy flat fields (можно оставить для совместимости, но основные данные теперь в JSON)
        'qty_value', 'qty_type', 'weight_value', 'volume_qty', 'container_number', 'container_type', 'seal_number'
    ];

    protected $casts = [
        'etd' => 'date',
        'eta' => 'date',
        'mother_etd' => 'date',
        'mother_eta' => 'date',
        'epu' => 'date',
        'feeder_eta' => 'date',
        // Автоматическое преобразование JSON <-> Array
        'packing_items' => 'array',
        'containers' => 'array',
        'notes' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function shipper()
    {
        // Если у вас есть модель Shipper, иначе можно удалить
        return $this->belongsTo(\App\Models\Shipper::class, 'shipper_id');
    }
}